<?php
namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\ManualTopUpRequest;
use App\Models\WithdrawRequest;
use App\Models\BankAccount;
use App\Enums\WalletTransactionType;
use App\Enums\WalletTransactionStatus;
use App\Enums\ManualTopUpStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;

class ManualWalletService
{
    /**
     * Create manual top up request
     */
    public function createManualTopUpRequest(User $user, float $amount): ManualTopUpRequest
    {
        $this->validateTopUpAmount($amount);

        return DB::transaction(function () use ($user, $amount) {
            $requestCode = $this->generateTopUpRequestCode();
            
            $topUpRequest = ManualTopUpRequest::create([
                'user_id' => $user->id,
                'request_code' => $requestCode,
                'amount' => $amount,
                'status' => ManualTopUpStatus::PENDING,
                'requested_at' => now(),
            ]);

            return $topUpRequest;
        });
    }

    /**
     * Get active bank accounts for top up
     */
    public function getActiveBankAccounts()
    {
        return BankAccount::where('is_active', true)->get();
    }

    /**
     * Update top up request to waiting payment status
     */
    public function setTopUpToWaitingPayment(ManualTopUpRequest $topUpRequest, int $bankAccountId): ManualTopUpRequest
    {
        $bankAccount = BankAccount::findOrFail($bankAccountId);
        
        $topUpRequest->update([
            'status' => ManualTopUpStatus::WAITING_PAYMENT,
            'bank_name' => $bankAccount->bank_name,
            'bank_account_number' => $bankAccount->account_number,
            'bank_account_name' => $bankAccount->account_name,
            'qr_code_url' => $bankAccount->qr_code_url,
        ]);

        return $topUpRequest->fresh();
    }

    /**
     * Upload payment proof
     */
    public function uploadPaymentProof(ManualTopUpRequest $topUpRequest, UploadedFile $file): ManualTopUpRequest
    {
        if ($topUpRequest->status !== ManualTopUpStatus::WAITING_PAYMENT) {
            throw ValidationException::withMessages([
                'file' => ['Status permintaan tidak valid untuk upload bukti pembayaran.'],
            ]);
        }

        $path = $file->store('payment-proofs', 'r2');
        
        $topUpRequest->update([
            'payment_proof_path' => $path,
            'status' => ManualTopUpStatus::WAITING_APPROVAL,
        ]);

        return $topUpRequest->fresh();
    }

    /**
     * Approve manual top up (admin)
     */
    public function approveTopUp(ManualTopUpRequest $topUpRequest, ?string $adminNotes = null): bool
    {
        if ($topUpRequest->status !== ManualTopUpStatus::WAITING_APPROVAL) {
            throw ValidationException::withMessages([
                'status' => ['Status permintaan tidak valid untuk disetujui.'],
            ]);
        }

        return DB::transaction(function () use ($topUpRequest, $adminNotes) {
            $wallet = $this->getOrCreateWallet($topUpRequest->user);
            
            // Add balance to wallet
            $wallet->addBalance(
                $topUpRequest->amount,
                'Top up manual - ' . $topUpRequest->request_code,
                WalletTransactionType::TOPUP,
                $topUpRequest->request_code
            );

            // Update top up request status
            $topUpRequest->update([
                'status' => ManualTopUpStatus::APPROVED,
                'admin_notes' => $adminNotes,
                'approved_at' => now(),
            ]);

            return true;
        });
    }

    /**
     * Reject manual top up (admin)
     */
    public function rejectTopUp(ManualTopUpRequest $topUpRequest, string $adminNotes): bool
    {
        if ($topUpRequest->status !== ManualTopUpStatus::WAITING_APPROVAL) {
            throw ValidationException::withMessages([
                'status' => ['Status permintaan tidak valid untuk ditolak.'],
            ]);
        }

        $topUpRequest->update([
            'status' => ManualTopUpStatus::REJECTED,
            'admin_notes' => $adminNotes,
            'rejected_at' => now(),
        ]);

        return true;
    }

    /**
     * Create manual withdraw request
     */
    public function createManualWithdrawRequest(User $user, float $amount, array $bankDetails): WithdrawRequest
    {
        $this->validateWithdrawAmount($amount);
        
        $wallet = $this->getOrCreateWallet($user);
        
        if (!$wallet->hasSufficientBalance($amount)) {
            throw ValidationException::withMessages([
                'amount' => ['Saldo tidak mencukupi untuk penarikan ini'],
            ]);
        }

        // Validasi bank account jika diperlukan
        $validation = $this->basicBankAccountValidation($bankDetails['bank_name'], $bankDetails['account_number']);
        
        if (!$validation['valid']) {
            throw ValidationException::withMessages([
                'account_number' => [$validation['message'] ?? 'Nomor rekening tidak valid'],
            ]);
        }

        return DB::transaction(function () use ($wallet, $amount, $bankDetails, $user) {
            $balanceBefore = $wallet->balance;
            
            // Create withdraw request
            $withdrawRequest = WithdrawRequest::create([
                'user_id' => $user->id,
                'withdrawal_code' => $this->generateWithdrawalCode(),
                'amount' => $amount,
                'admin_fee' => $this->calculateAdminFee($amount),
                'bank_name' => $bankDetails['bank_name'],
                'account_number' => $bankDetails['account_number'],
                'account_name' => $bankDetails['account_name'],
                'status' => 'pending',
                'requested_at' => now(),
            ]);

            // Deduct balance immediately
            $wallet->decrement('balance', $amount);
            
            // Create transaction record
            $transaction = $this->createTransaction($wallet, [
                'type' => WalletTransactionType::WITHDRAW,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->fresh()->balance,
                'description' => 'Penarikan manual ke ' . $bankDetails['bank_name'] . ' - ' . $bankDetails['account_number'],
                'status' => WalletTransactionStatus::PENDING,
                'reference_id' => $withdrawRequest->withdrawal_code,
            ]);

            return $withdrawRequest;
        });
    }

    /**
     * Process manual withdraw (admin)
     */
    public function processManualWithdraw(WithdrawRequest $withdrawRequest, string $status, ?string $adminNotes): bool
    {
        return DB::transaction(function () use ($withdrawRequest, $status, $adminNotes) {
            $oldStatus = $withdrawRequest->status;
            
            $withdrawRequest->update([
                'status' => $status,
                'admin_notes' => $adminNotes,
                'processed_at' => now(),
                'completed_at' => in_array($status, ['completed', 'failed']) ? now() : null,
            ]);

            // Update related transaction
            $transaction = WalletTransaction::where('reference_id', $withdrawRequest->withdrawal_code)->first();
            
            if ($transaction) {
                $newTransactionStatus = match($status) {
                    'completed' => WalletTransactionStatus::SUCCESS,
                    'failed' => WalletTransactionStatus::FAILED,
                    'cancelled' => WalletTransactionStatus::CANCELLED,
                    'processing' => WalletTransactionStatus::PENDING,
                    default => WalletTransactionStatus::PENDING,
                };

                // If failed or cancelled, refund the balance
                if (in_array($status, ['failed', 'cancelled']) && $oldStatus === 'pending') {
                    $wallet = $transaction->wallet;
                    $wallet->increment('balance', $withdrawRequest->amount);
                    
                    $transaction->update([
                        'status' => $newTransactionStatus,
                        'balance_after' => $wallet->fresh()->balance,
                    ]);
                } else {
                    $transaction->update(['status' => $newTransactionStatus]);
                }
            }

            return true;
        });
    }

    /**
     * Get user's manual top up requests
     */
    public function getUserTopUpRequests(User $user, int $perPage = 10)
    {
        return ManualTopUpRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get top up request by code
     */
    public function getTopUpRequestByCode(string $requestCode): ?ManualTopUpRequest
    {
        return ManualTopUpRequest::where('request_code', $requestCode)->first();
    }

    /**
     * Cancel top up request
     */
    public function cancelTopUpRequest(ManualTopUpRequest $topUpRequest): bool
    {
        if (!in_array($topUpRequest->status, [ManualTopUpStatus::PENDING, ManualTopUpStatus::WAITING_PAYMENT])) {
            throw ValidationException::withMessages([
                'status' => ['Permintaan tidak dapat dibatalkan pada status ini.'],
            ]);
        }

        $topUpRequest->update([
            'status' => ManualTopUpStatus::CANCELLED,
        ]);

        return true;
    }

    // Private helper methods
    private function getOrCreateWallet(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'pending_balance' => 0]
        );
    }

    private function validateTopUpAmount(float $amount): void
    {
        if ($amount < 10000) {
            throw ValidationException::withMessages([
                'amount' => ['Minimum top up adalah Rp 10.000'],
            ]);
        }

        if ($amount > 10000000) {
            throw ValidationException::withMessages([
                'amount' => ['Maksimum top up adalah Rp 10.000.000'],
            ]);
        }
    }

    private function validateWithdrawAmount(float $amount): void
    {
        if ($amount < 50000) {
            throw ValidationException::withMessages([
                'amount' => ['Minimum penarikan adalah Rp 50.000'],
            ]);
        }
    }

    private function generateTopUpRequestCode(): string
    {
        return 'MTOP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    private function generateWithdrawalCode(): string
    {
        return 'MWDR-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    private function calculateAdminFee(float $amount): float
    {
        return $amount < 1000000 ? 2500 : 5000;
    }

    private function basicBankAccountValidation(string $bankName, string $accountNumber): array
    {
        if (empty($bankName) || empty($accountNumber)) {
            return ['valid' => false, 'message' => 'Nama bank dan nomor rekening harus diisi'];
        }

        if (!preg_match('/^[0-9]+$/', $accountNumber)) {
            return ['valid' => false, 'message' => 'Nomor rekening hanya boleh berisi angka'];
        }

        if (strlen($accountNumber) < 8 || strlen($accountNumber) > 20) {
            return ['valid' => false, 'message' => 'Nomor rekening harus 8-20 digit'];
        }

        return ['valid' => true];
    }

    private function createTransaction(Wallet $wallet, array $data): WalletTransaction
    {
        return WalletTransaction::create(array_merge([
            'wallet_id' => $wallet->id,
        ], $data));
    }
    public function hasPendingManualRequests(User $user): array
    {
        $pendingTopUp = ManualTopUpRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'waiting_payment', 'waiting_approval'])
            ->count();
            
        $pendingWithdraw = WithdrawRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
            
        return [
            'has_pending' => ($pendingTopUp > 0 || $pendingWithdraw > 0),
            'pending_topup' => $pendingTopUp,
            'pending_withdraw' => $pendingWithdraw
        ];
    }
    public function getResumableTopUpRequests(User $user)
    {
        return ManualTopUpRequest::where('user_id', $user->id)
            ->whereIn('status', [
                ManualTopUpStatus::PENDING, 
                ManualTopUpStatus::WAITING_PAYMENT
            ])
            ->orderBy('created_at', 'desc')
            ->get(); 
    }
}