<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\BankAccount;
use App\Enums\WalletTransactionType;
use App\Enums\WalletTransactionStatus;
use App\Events\PaymentStatusChanged;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;

class WalletService
{
    /**
     * Get or create wallet for user
     */
    public function getOrCreateWallet(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'pending_balance' => 0]
        );
    }

    /**
     * Create top up transaction
     */
    public function createTopUpTransaction(User $user, float $amount): WalletTransaction
    {
        $this->validateTopUpAmount($amount);

        return DB::transaction(function () use ($user, $amount) {
            $wallet = $this->getOrCreateWallet($user);
            $referenceId = $this->generateReferenceId('TOPUP', $user->id);
            
            return $this->createTransaction($wallet, [
                'type' => WalletTransactionType::TOPUP,
                'amount' => $amount,
                'balance_before' => $wallet->balance,
                'balance_after' => $wallet->balance, 
                'description' => 'Top up saldo',
                'status' => WalletTransactionStatus::PENDING,
                'reference_id' => $referenceId,
                'requested_at' => now(),
            ]);
        });
    }

    /**
     * Set top up to waiting payment status
     */
    public function setTopUpToWaitingPayment(WalletTransaction $transaction, int $bankAccountId): WalletTransaction
    {
        $bankAccount = BankAccount::findOrFail($bankAccountId);
        
        $transaction->update([
            'bank_name' => $bankAccount->bank_name,
            'bank_account_number' => $bankAccount->account_number,
            'bank_account_name' => $bankAccount->account_name,
            'qr_code_url' => $bankAccount->qr_code_path,
        ]);

        return $transaction->fresh();
    }

    /**
     * Upload payment proof for top up
     */
    public function uploadPaymentProof(WalletTransaction $transaction, UploadedFile $file): WalletTransaction
    {
        if ($transaction->type !== WalletTransactionType::TOPUP || $transaction->status !== WalletTransactionStatus::PENDING) {
            throw ValidationException::withMessages([
                'file' => ['Status transaksi tidak valid untuk upload bukti pembayaran.'],
            ]);
        }

        $path = $file->store('payment-proofs', 'r2');
        
        $transaction->update([
            'payment_proof_path' => $path,
        ]);

        return $transaction->fresh();
    }

    /**
     * Approve top up (admin)
     */
    public function approveTopUp(WalletTransaction $transaction, ?string $adminNotes = null): bool
    {
        if ($transaction->type !== WalletTransactionType::TOPUP || 
            $transaction->status !== WalletTransactionStatus::PENDING ||
            !$transaction->payment_proof_path) {
            throw ValidationException::withMessages([
                'status' => ['Status transaksi tidak valid untuk disetujui.'],
            ]);
        }

        return DB::transaction(function () use ($transaction, $adminNotes) {
            $wallet = $transaction->wallet;
            
            // Add balance to wallet
            $balanceBefore = $wallet->balance;
            $wallet->increment('balance', $transaction->amount);
            
            // Update transaction
            $transaction->update([
                'status' => WalletTransactionStatus::SUCCESS,
                'admin_notes' => $adminNotes,
                'approved_at' => now(),
                'completed_at' => now(),
                'balance_after' => $wallet->fresh()->balance,
            ]);

            return true;
        });
    }

    /**
     * Reject top up (admin)
     */
    public function rejectTopUp(WalletTransaction $transaction, string $adminNotes): bool
    {
        if ($transaction->type !== WalletTransactionType::TOPUP || 
            $transaction->status !== WalletTransactionStatus::PENDING ||
            !$transaction->payment_proof_path) {
            throw ValidationException::withMessages([
                'status' => ['Status transaksi tidak valid untuk ditolak.'],
            ]);
        }

        $transaction->update([
            'status' => WalletTransactionStatus::FAILED,
            'admin_notes' => $adminNotes,
            'rejected_at' => now(),
            'completed_at' => now(),
        ]);

        return true;
    }

    /**
     * Create withdraw request
     */
    public function createWithdrawRequest(User $user, float $amount, array $bankDetails): WalletTransaction
    {
        $this->validateWithdrawAmount($amount);
        
        $wallet = $this->getOrCreateWallet($user);
        
        if (!$wallet->hasSufficientBalance($amount)) {
            throw ValidationException::withMessages([
                'amount' => ['Saldo tidak mencukupi untuk penarikan ini'],
            ]);
        }

        $validation = $this->basicBankAccountValidation($bankDetails['bank_name'], $bankDetails['account_number']);
        
        if (!$validation['valid']) {
            throw ValidationException::withMessages([
                'account_number' => [$validation['message'] ?? 'Nomor rekening tidak valid'],
            ]);
        }

        return DB::transaction(function () use ($wallet, $amount, $bankDetails, $user) {
            $balanceBefore = $wallet->balance;
            $adminFee = $this->calculateAdminFee($amount);
            
            // Deduct balance immediately
            $wallet->decrement('balance', $amount);
            
            $transaction = $this->createTransaction($wallet, [
                'type' => WalletTransactionType::WITHDRAW,
                'amount' => $amount,
                'admin_fee' => $adminFee,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->fresh()->balance,
                'description' => 'Penarikan saldo ke ' . $bankDetails['bank_name'] . ' - ' . $bankDetails['account_number'],
                'status' => WalletTransactionStatus::PENDING,
                'reference_id' => $this->generateReferenceId('WD', $user->id),
                'bank_name' => $bankDetails['bank_name'],
                'account_number' => $bankDetails['account_number'],
                'account_name' => $bankDetails['account_name'],
                'requested_at' => now(),
            ]);

            // event(new WithdrawRequestCreated($user, $transaction, $transaction));
          
            return $transaction;
        });
    }
   
    /**
     * Process withdraw request (admin)
     */
    public function processWithdrawRequest(WalletTransaction $transaction, string $status, ?string $adminNotes): bool
    {
        if ($transaction->type !== WalletTransactionType::WITHDRAW) {
            throw ValidationException::withMessages([
                'transaction' => ['Transaksi bukan transaksi penarikan.'],
            ]);
        }

        return DB::transaction(function () use ($transaction, $status, $adminNotes) {
            $oldStatus = $transaction->status->value;
            
            $newStatus = match($status) {
                'processing' => WalletTransactionStatus::PROCESSING,
                'completed' => WalletTransactionStatus::SUCCESS,
                'failed' => WalletTransactionStatus::FAILED,
                'cancelled' => WalletTransactionStatus::CANCELLED,
                default => throw new \InvalidArgumentException('Invalid status: ' . $status),
            };

            $updateData = [
                'status' => $newStatus,
                'admin_notes' => $adminNotes,
                'processed_at' => now(),
            ];

            if (in_array($status, ['completed', 'failed', 'cancelled'])) {
                $updateData['completed_at'] = now();
            }

            $transaction->update($updateData);

            // If failed or cancelled, refund the balance
            if (in_array($status, ['failed', 'cancelled']) && $oldStatus === 'pending') {
                $wallet = $transaction->wallet;
                $wallet->increment('balance', $transaction->amount);
                
                $transaction->update([
                    'balance_after' => $wallet->fresh()->balance,
                ]);
            }

            // event(new WithdrawRequestStatusChanged($transaction->wallet->user, $transaction, $transaction, $oldStatus, $status));

            return true;
        });
    }

    /**
     * Get transaction history for user
     */
    public function getTransactionHistory(User $user, int $perPage = 10): LengthAwarePaginator
    {
        $wallet = $this->getOrCreateWallet($user);
        
        return $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get withdraw requests for user
     */
    public function getWithdrawRequests(User $user, int $perPage = 10): LengthAwarePaginator
    {
        $wallet = $this->getOrCreateWallet($user);
        
        return $wallet->transactions()
            ->where('type', WalletTransactionType::WITHDRAW)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get top up requests for user
     */
    public function getTopUpRequests(User $user, int $perPage = 10): LengthAwarePaginator
    {
        $wallet = $this->getOrCreateWallet($user);
        
        return $wallet->transactions()
            ->where('type', WalletTransactionType::TOPUP)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get transaction by ID for user
     */
    public function getTransactionById(User $user, int $transactionId): ?WalletTransaction
    {
        $wallet = $this->getOrCreateWallet($user);
        
        return $wallet->transactions()
            ->where('id', $transactionId)
            ->first();
    }

    /**
     * Get transaction by reference ID
     */
    public function getTransactionByReferenceId(string $referenceId): ?WalletTransaction
    {
        return WalletTransaction::where('reference_id', $referenceId)->first();
    }

    /**
     * Cancel pending transaction
     */
    public function cancelTransaction(User $user, int $transactionId): bool
    {
        $transaction = $this->getTransactionById($user, $transactionId);

        if (!$transaction || !$transaction->canBeCancelled()) {
            throw ValidationException::withMessages([
                'transaction' => ['Transaksi ini tidak ada atau tidak dapat dibatalkan.'],
            ]);
        }

        return DB::transaction(function () use ($transaction, $user) {
            $oldStatus = $transaction->status->value;

            if ($transaction->isWithdrawal() && $transaction->status === WalletTransactionStatus::PENDING) {
                // Refund balance for cancelled withdrawal
                $wallet = $transaction->wallet;
                $wallet->increment('balance', $transaction->amount);
                
                $transaction->update([
                    'status' => WalletTransactionStatus::CANCELLED,
                    'balance_after' => $wallet->fresh()->balance,
                    'completed_at' => now(),
                ]);
            } else {
                $transaction->update([
                    'status' => WalletTransactionStatus::CANCELLED,
                    'completed_at' => now(),
                ]);
            }
            
            event(new PaymentStatusChanged($transaction, $oldStatus, WalletTransactionStatus::CANCELLED->value));

            return true;
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
     * Check if user has pending requests
     */
    public function hasPendingRequests(User $user): array
    {
        $wallet = $this->getOrCreateWallet($user);
        
        $pendingTopUp = $wallet->transactions()
            ->where('type', WalletTransactionType::TOPUP)
            ->where('status', WalletTransactionStatus::PENDING)
            ->count();
            
        $pendingWithdraw = $wallet->transactions()
            ->where('type', WalletTransactionType::WITHDRAW)
            ->where('status', WalletTransactionStatus::PENDING)
            ->count();
            
        return [
            'has_pending' => ($pendingTopUp > 0 || $pendingWithdraw > 0),
            'pending_topup' => $pendingTopUp,
            'pending_withdraw' => $pendingWithdraw
        ];
    }

    /**
     * Get resumable top up requests for user
     */
    public function getResumableTopUpRequests(User $user)
    {
        $wallet = $this->getOrCreateWallet($user);
        
        return $wallet->transactions()
            ->where('type', WalletTransactionType::TOPUP)
            ->where('status', WalletTransactionStatus::PENDING)
            ->where(function($query) {
                $query->whereNull('payment_proof_path')
                      ->orWhereNull('bank_name');
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Validate bank account (validasi dasar tanpa API)
     */
    public function validateBankAccount(string $bankCode, string $accountNumber): array
    {
        return $this->basicBankAccountValidation($bankCode, $accountNumber);
    }

    // Private helper methods
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

    private function generateReferenceId(string $prefix, int $userId): string
    {
        return $prefix . '-' . date('Ymd') . '-' . $userId . '-' . strtoupper(substr(uniqid(), -6));
    }

    private function createTransaction(Wallet $wallet, array $data): WalletTransaction
    {
        return WalletTransaction::create(array_merge([
            'wallet_id' => $wallet->id,
        ], $data));
    }

    private function calculateAdminFee(float $amount): float
    {
        return $amount < 1000000 ? 2500 : 5000;
    }

    private function basicBankAccountValidation(string $bankCode, string $accountNumber): array
    {
        // $rules = [
        //     'BCA' => ['min' => 10, 'max' => 10, 'pattern' => '/^[0-9]{10}$/'],
        //     'BNI' => ['min' => 10, 'max' => 10, 'pattern' => '/^[0-9]{10}$/'],
        //     'BRI' => ['min' => 15, 'max' => 15, 'pattern' => '/^[0-9]{15}$/'],
        //     'Mandiri' => ['min' => 13, 'max' => 13, 'pattern' => '/^[0-9]{13}$/'],
        //     'CIMB Niaga' => ['min' => 13, 'max' => 14, 'pattern' => '/^[0-9]{13,14}$/'],
        //     'Danamon' => ['min' => 10, 'max' => 10, 'pattern' => '/^[0-9]{10}$/'],
        //     'Permata' => ['min' => 10, 'max' => 10, 'pattern' => '/^[0-9]{10}$/'],
        //     'BTN' => ['min' => 16, 'max' => 16, 'pattern' => '/^[0-9]{16}$/'],
        // ];

        // $rule = $rules[$bankCode] ?? ['min' => 8, 'max' => 20, 'pattern' => '/^[0-9]{8,20}$/'];

        // if (!preg_match($rule['pattern'], $accountNumber)) {
        //     return [
        //         'valid' => false, 
        //         'message' => "Format nomor rekening {$bankCode} tidak valid"
        //     ];
        // }

        return ['valid' => true];
    }
}