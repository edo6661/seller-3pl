<?php

namespace App\Services;

use App\Models\WithdrawRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WithdrawService
{
    protected $walletService;
    protected $notificationService;

    public function __construct(WalletService $walletService, NotificationService $notificationService)
    {
        $this->walletService = $walletService;
        $this->notificationService = $notificationService;
    }

    public function getUserWithdrawals(int $userId): Collection
    {
        return WithdrawRequest::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getWithdrawById(int $id): ?WithdrawRequest
    {
        return WithdrawRequest::with('user')->find($id);
    }

    public function createWithdrawRequest(array $data): WithdrawRequest
    {
        return DB::transaction(function () use ($data) {
            $userId = $data['user_id'];
            $amount = $data['amount'];

            // Check balance
            if (!$this->walletService->hasBalance($userId, $amount)) {
                throw new \Exception('Saldo tidak mencukupi');
            }

            // Calculate admin fee (example: 2.5% with min 5000)
            $adminFee = max($amount * 0.025, 5000);
            $totalDeduction = $amount + $adminFee;

            if (!$this->walletService->hasBalance($userId, $totalDeduction)) {
                throw new \Exception('Saldo tidak mencukupi untuk biaya admin');
            }

            // Create withdrawal request
            $data['admin_fee'] = $adminFee;
            $data['status'] = 'pending';
            $withdrawal = WithdrawRequest::create($data);

            // Add to pending balance
            $this->walletService->addPendingBalance($userId, $totalDeduction);

            // Create notification
            $this->notificationService->createForUser(
                $userId,
                'withdrawal_requested',
                'Permintaan Penarikan Dibuat',
                "Permintaan penarikan sebesar Rp " . number_format($amount, 0, ',', '.') . " telah dibuat dan sedang diproses."
            );

            return $withdrawal;
        });
    }

    public function processWithdrawal(int $withdrawalId, string $status, ?string $adminNotes = null): WithdrawRequest
    {
        return DB::transaction(function () use ($withdrawalId, $status, $adminNotes) {
            $withdrawal = WithdrawRequest::findOrFail($withdrawalId);

            if ($withdrawal->status !== 'pending') {
                throw new \Exception('Penarikan sudah diproses sebelumnya');
            }

            $totalAmount = $withdrawal->amount + $withdrawal->admin_fee;

            switch ($status) {
                case 'processing':
                    $withdrawal->update([
                        'status' => 'processing',
                        'processed_at' => now(),
                        'admin_notes' => $adminNotes
                    ]);

                    $this->notificationService->createForUser(
                        $withdrawal->user_id,
                        'withdrawal_processing',
                        'Penarikan Sedang Diproses',
                        "Permintaan penarikan {$withdrawal->withdrawal_code} sedang diproses."
                    );
                    break;

                case 'completed':
                    // Deduct from wallet and remove from pending
                    $this->walletService->deductBalance(
                        $withdrawal->user_id,
                        $totalAmount,
                        "Penarikan {$withdrawal->withdrawal_code}",
                        'withdraw',
                        $withdrawal->id
                    );

                    $this->walletService->deductPendingBalance($withdrawal->user_id, $totalAmount);

                    $withdrawal->update([
                        'status' => 'completed',
                        'processed_at' => $withdrawal->processed_at ?? now(),
                        'completed_at' => now(),
                        'admin_notes' => $adminNotes
                    ]);

                    $this->notificationService->createForUser(
                        $withdrawal->user_id,
                        'withdrawal_completed',
                        'Penarikan Berhasil',
                        "Penarikan {$withdrawal->withdrawal_code} telah berhasil diproses ke rekening Anda."
                    );
                    break;

                case 'rejected':
                    // Remove from pending balance
                    $this->walletService->deductPendingBalance($withdrawal->user_id, $totalAmount);

                    $withdrawal->update([
                        'status' => 'rejected',
                        'processed_at' => now(),
                        'admin_notes' => $adminNotes ?? 'Permintaan penarikan ditolak'
                    ]);

                    $this->notificationService->createForUser(
                        $withdrawal->user_id,
                        'withdrawal_rejected',
                        'Penarikan Ditolak',
                        "Permintaan penarikan {$withdrawal->withdrawal_code} ditolak. {$adminNotes}"
                    );
                    break;

                default:
                    throw new \Exception('Status tidak valid');
            }

            return $withdrawal;
        });
    }

    public function getWithdrawalsByStatus(string $status): Collection
    {
        return WithdrawRequest::where('status', $status)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPendingWithdrawals(): Collection
    {
        return $this->getWithdrawalsByStatus('pending');
    }

    public function getProcessingWithdrawals(): Collection
    {
        return $this->getWithdrawalsByStatus('processing');
    }

    public function getBatchWithdrawals(string $batchTime): Collection
    {
        $query = WithdrawRequest::where('status', 'pending')
            ->with('user');

        if ($batchTime === 'morning') {
            $query->morningBatch();
        } else {
            $query->afternoonBatch();
        }

        return $query->orderBy('created_at', 'asc')->get();
    }

    public function getWithdrawalStats(): array
    {
        $total = WithdrawRequest::count();
        $pending = WithdrawRequest::pending()->count();
        $processing = WithdrawRequest::processing()->count();
        $completed = WithdrawRequest::completed()->count();
        $rejected = WithdrawRequest::where('status', 'rejected')->count();

        $totalAmount = WithdrawRequest::completed()->sum('amount');
        $totalFees = WithdrawRequest::completed()->sum('admin_fee');

        return [
            'total' => $total,
            'pending' => $pending,
            'processing' => $processing,
            'completed' => $completed,
            'rejected' => $rejected,
            'total_amount' => $totalAmount,
            'total_fees' => $totalFees,
            'success_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0
        ];
    }

    public function searchWithdrawals(string $search): Collection
    {
        return WithdrawRequest::with('user')
            ->where(function ($query) use ($search) {
                $query->where('withdrawal_code', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%")
                    ->orWhere('account_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getMinimumWithdrawal(): float
    {
        return 50000; // Minimum withdrawal amount
    }

    public function calculateAdminFee(float $amount): float
    {
        return max($amount * 0.025, 5000); // 2.5% with minimum 5000
    }

    public function canWithdraw(int $userId, float $amount): array
    {
        $minAmount = $this->getMinimumWithdrawal();
        $adminFee = $this->calculateAdminFee($amount);
        $totalRequired = $amount + $adminFee;

        $errors = [];

        if ($amount < $minAmount) {
            $errors[] = "Minimum penarikan adalah Rp " . number_format($minAmount, 0, ',', '.');
        }

        if (!$this->walletService->hasBalance($userId, $totalRequired)) {
            $errors[] = "Saldo tidak mencukupi. Dibutuhkan Rp " . number_format($totalRequired, 0, ',', '.') . " (termasuk biaya admin Rp " . number_format($adminFee, 0, ',', '.') . ")";
        }

        // Check if user has pending withdrawal
        $pendingWithdrawal = WithdrawRequest::where('user_id', $userId)
            ->whereIn('status', ['pending', 'processing'])
            ->exists();

        if ($pendingWithdrawal) {
            $errors[] = "Anda masih memiliki permintaan penarikan yang belum selesai diproses.";
        }

        return [
            'can_withdraw' => empty($errors),
            'errors' => $errors,
            'admin_fee' => $adminFee,
            'total_deduction' => $totalRequired
        ];
    }
}
