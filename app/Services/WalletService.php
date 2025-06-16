<?php
namespace App\Services;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawRequest;
use App\Enums\WalletTransactionType;
use App\Enums\WalletTransactionStatus;
use App\Events\PaymentStatusChanged;
use App\Events\WithdrawRequestCreated;
use App\Events\WithdrawRequestStatusChanged;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class WalletService
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

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
     * Create top up transaction with Midtrans
     */
    public function createTopUpTransaction(User $user, float $amount, array $paymentMethods = []): array
    {
        $this->validateTopUpAmount($amount);

        return DB::transaction(function () use ($user, $amount, $paymentMethods) {
            $wallet = $this->getOrCreateWallet($user);
            $orderId = $this->generateOrderId('TOPUP', $user->id);
            
            $transaction = $this->createTransaction($wallet, [
                'type' => WalletTransactionType::TOPUP,
                'amount' => $amount,
                'balance_before' => $wallet->balance,
                'balance_after' => $wallet->balance, 
                'description' => 'Top up saldo via Midtrans',
                'status' => WalletTransactionStatus::PENDING,
                'reference_id' => $orderId,
            ]);

            $payload = $this->buildMidtransPayloadTopUp($orderId, $amount, $user, $paymentMethods);

            try {
                $transaction = $this->midtransService->createTransaction($transaction, $payload);
                
                return [
                    'success' => true,
                    'snap_token' => $transaction->snap_token,
                    'snap_url' => $transaction->snap_url,
                    'transaction_id' => $transaction->id,
                    'order_id' => $orderId,
                ];
            } catch (\Exception $e) {
                $transaction->update(['status' => WalletTransactionStatus::FAILED]);
                
                Log::error('Midtrans transaction creation failed', [
                    'error' => $e->getMessage(),
                    'order_id' => $orderId,
                    'transaction_id' => $transaction->id
                ]);
                
                throw ValidationException::withMessages([
                    'amount' => ['Gagal membuat transaksi pembayaran. Silakan coba lagi.'],
                ]);
            }
        });
    }

    /**
     * Create withdraw request (manual process tanpa API pihak ketiga)
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

            
            $wallet->decrement('balance', $amount);
            
            
            $transaction = $this->createTransaction($wallet, [
                'type' => WalletTransactionType::WITHDRAW,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->fresh()->balance,
                'description' => 'Penarikan saldo ke ' . $bankDetails['bank_name'] . ' - ' . $bankDetails['account_number'],
                'status' => WalletTransactionStatus::PENDING,
                'reference_id' => $withdrawRequest->withdrawal_code,
            ]);

            
            event(new WithdrawRequestCreated($user, $withdrawRequest, $transaction));
          
            
            return $transaction;
        });
    }
   
    /**
     * Process withdraw request (untuk admin)
     */
    public function processWithdrawRequest(WithdrawRequest $withdrawRequest, string $status, ?string $adminNotes): bool
    {
        return DB::transaction(function () use ($withdrawRequest, $status, $adminNotes) {
            $oldStatus = $withdrawRequest->status;
            
            $withdrawRequest->update([
                'status' => $status,
                'admin_notes' => $adminNotes,
                'processed_at' => now(),
                'completed_at' => in_array($status, ['completed', 'failed']) ? now() : null,
            ]);

            
            $transaction = WalletTransaction::where('reference_id', $withdrawRequest->withdrawal_code)->first();
            
            if ($transaction) {
                $newTransactionStatus = match($status) {
                    'completed' => WalletTransactionStatus::SUCCESS,
                    'failed' => WalletTransactionStatus::FAILED,
                    'cancelled' => WalletTransactionStatus::CANCELLED,
                    'processing' => WalletTransactionStatus::PENDING,
                    default => WalletTransactionStatus::PENDING,
                };

                
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

                
                event(new WithdrawRequestStatusChanged($withdrawRequest->user, $withdrawRequest, $transaction, $oldStatus, $status));
            }

            return true;
        });
    }

    /**
     * Handle Midtrans notification/callback
     */
    public function handleMidtransNotification(array $notification): bool
    {
        try {
            
            $transaction = $this->midtransService->handleNotification($notification);
            
            
            if ($transaction->status === WalletTransactionStatus::SUCCESS && 
                $transaction->type === WalletTransactionType::TOPUP) {
                $this->processSuccessfulTopUp($transaction);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Midtrans notification processing failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update transaction status berdasarkan hasil dari Midtrans
     */
    public function updateTransactionStatus(string $orderId, string $midtransStatus): WalletTransaction
    {
        try {
            $transaction = WalletTransaction::where('reference_id', $orderId)->firstOrFail();
            $oldStatus = $transaction->status->value;
            
            $newStatus = $this->mapMidtransStatusToInternal($midtransStatus);
            
            DB::transaction(function () use ($transaction, $newStatus, $oldStatus) {
                $transaction->update(['status' => $newStatus]);
                
                if ($newStatus === WalletTransactionStatus::SUCCESS && 
                    $transaction->type === WalletTransactionType::TOPUP) {
                    $this->processSuccessfulTopUp($transaction);
                }
                
                if ($oldStatus !== $newStatus->value) {
                    event(new PaymentStatusChanged($transaction, $oldStatus, $newStatus->value));
                }
            });

          
            return $transaction->fresh();
        } catch (\Exception $e) {
            Log::error('Update transaction status error: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'midtrans_status' => $midtransStatus
            ]);
            throw $e;
        }
    }

    /**
     * Finish transaction - dipanggil dari controller ketika user kembali dari payment page
     */
    public function finishTransaction(string $orderId): WalletTransaction
    {
        try {
            $statusResponse = $this->midtransService->getStatus($orderId);
            
            if ($statusResponse['success']) {
                $midtransStatus = $statusResponse['data']['transaction_status'];
                return $this->updateTransactionStatus($orderId, $midtransStatus);
            } else {
                throw new \Exception('Failed to get transaction status from Midtrans');
            }
        } catch (\Exception $e) {
            Log::error('Finish transaction error: ' . $e->getMessage(), [
                'order_id' => $orderId
            ]);
            throw $e;
        }
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
        return WithdrawRequest::where('user_id', $user->id)
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
     * Get transaction by order ID
     */
    public function getTransactionByOrderId(string $orderId): ?WalletTransaction
    {
        return WalletTransaction::where('reference_id', $orderId)->first();
    }

    /**
     * Check transaction status via Midtrans API
     */
    public function checkTransactionStatus(string $orderId): array
    {
        try {
            return $this->midtransService->getStatus($orderId);
        } catch (\Exception $e) {
            Log::error('Check transaction status error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
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

            if ($transaction->isWithdrawal()) {
                $withdrawRequest = WithdrawRequest::where('withdrawal_code', $transaction->reference_id)
                                                  ->where('user_id', $user->id)
                                                  ->first();

                if (!$withdrawRequest || $withdrawRequest->status !== 'pending') {
                    throw ValidationException::withMessages([
                        'transaction' => ['Permintaan penarikan terkait tidak dapat dibatalkan.'],
                    ]);
                }
                // todo: logika untuk membatalkan penarikan midtrans

                $wallet = $transaction->wallet;
                $wallet->increment('balance', $transaction->amount);
                
                $withdrawRequest->update(['status' => 'cancelled']);

                $transaction->update([
                    'status' => WalletTransactionStatus::CANCELLED,
                ]);
                

            } else if ($transaction->isTopup()) {
                
                // TODO: logika untuk batalin top up midtrans
                $transaction->update(['status' => WalletTransactionStatus::CANCELLED]);
            }
            
            event(new PaymentStatusChanged($transaction, $oldStatus, WalletTransactionStatus::CANCELLED->value));

            return true;
        });
    }

    

    /**
     * Validate bank account (validasi dasar tanpa API)
     */
    public function validateBankAccount(string $bankCode, string $accountNumber): array
    {
        return $this->basicBankAccountValidation($bankCode, $accountNumber);
    }

    /**
     * Process successful top up - Private method
     */
    private function processSuccessfulTopUp(WalletTransaction $transaction): void
    {
        if ($transaction->wallet->balance !== $transaction->balance_before) {
            return; 
        }

        DB::transaction(function () use ($transaction) {
            $wallet = $transaction->wallet;
            $wallet->increment('balance', $transaction->amount);
            
            $transaction->update([
                'balance_after' => $wallet->fresh()->balance,
            ]);
            
        });
    }

    /**
     * Generate unique withdrawal code
     */
    private function generateWithdrawalCode(): string
    {
        do {
            $code = 'WD' . date('Ymd') . strtoupper(substr(uniqid(), -6));
        } while (WithdrawRequest::where('withdrawal_code', $code)->exists());

        return $code;
    }

    /**
     * Calculate admin fee for withdrawal
     */
    private function calculateAdminFee(float $amount): float
    {
        
        return $amount < 1000000 ? 2500 : 5000;
    }

    /**
     * Map Midtrans status to internal status
     */
    private function mapMidtransStatusToInternal(string $midtransStatus): WalletTransactionStatus
    {
        return match($midtransStatus) {
            'settlement', 'capture' => WalletTransactionStatus::SUCCESS,
            'pending' => WalletTransactionStatus::PENDING,
            'expire', 'deny' => WalletTransactionStatus::FAILED,
            'cancel' => WalletTransactionStatus::CANCELLED,
            default => WalletTransactionStatus::FAILED,
        };
    }

    /**
     * Validate top up amount
     */
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

    /**
     * Validate withdraw amount
     */
    private function validateWithdrawAmount(float $amount): void
    {
        if ($amount < 50000) {
            throw ValidationException::withMessages([
                'amount' => ['Minimum penarikan adalah Rp 50.000'],
            ]);
        }
    }

    /**
     * Generate unique order ID
     */
    private function generateOrderId(string $prefix, int $userId): string
    {
        return $prefix . '-' . time() . '-' . $userId . '-' . rand(1000, 9999);
    }

    /**
     * Create wallet transaction record
     */
    private function createTransaction(Wallet $wallet, array $data): WalletTransaction
    {
        return WalletTransaction::create(array_merge([
            'wallet_id' => $wallet->id,
        ], $data));
    }

    /**
     * Build Midtrans payment payload
     */
    private function buildMidtransPayloadTopUp(string $orderId, float $amount, User $user, array $paymentMethods = []): array
    {
        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'item_details' => [
                [
                    'id' => 'topup-wallet',
                    'price' => (int) $amount,
                    'quantity' => 1,
                    'name' => 'Top Up Saldo Dompet',
                ]
            ],
            'callbacks' => [
                'finish' => route('seller.wallet.topup.finish'),
            ],
            'expiry' => [
                'start_time' => date('Y-m-d H:i:s O'),
                'unit' => 'minutes',
                'duration' => 60 
            ]
        ];

        if (!empty($paymentMethods)) {
            $payload['enabled_payments'] = $paymentMethods;
        }

        return $payload;
    }

    /**
     * Basic bank account validation (tanpa API pihak ketiga)
     */
    private function basicBankAccountValidation(string $bankCode, string $accountNumber): array
    {
        $rules = [
            'BCA' => ['min' => 10, 'max' => 10, 'pattern' => '/^[0-9]{10}$/'],
            'BNI' => ['min' => 10, 'max' => 10, 'pattern' => '/^[0-9]{10}$/'],
            'BRI' => ['min' => 15, 'max' => 15, 'pattern' => '/^[0-9]{15}$/'],
            'Mandiri' => ['min' => 13, 'max' => 13, 'pattern' => '/^[0-9]{13}$/'],
            'CIMB Niaga' => ['min' => 13, 'max' => 14, 'pattern' => '/^[0-9]{13,14}$/'],
            'Danamon' => ['min' => 10, 'max' => 10, 'pattern' => '/^[0-9]{10}$/'],
            'Permata' => ['min' => 10, 'max' => 10, 'pattern' => '/^[0-9]{10}$/'],
            'BTN' => ['min' => 16, 'max' => 16, 'pattern' => '/^[0-9]{16}$/'],
        ];

        $rule = $rules[$bankCode] ?? ['min' => 8, 'max' => 20, 'pattern' => '/^[0-9]{8,20}$/'];

        if (!preg_match($rule['pattern'], $accountNumber)) {
            return [
                'valid' => false, 
                'message' => "Format nomor rekening {$bankCode} tidak valid"
            ];
        }

        return ['valid' => true];
    }
}