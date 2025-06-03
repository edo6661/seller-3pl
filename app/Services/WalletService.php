<?php
namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Enums\WalletTransactionType;
use App\Enums\WalletTransactionStatus;
use App\Events\PaymentStatusChanged;
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
                
                Log::info('Top-up transaction created successfully', [
                    'order_id' => $orderId,
                    'transaction_id' => $transaction->id,
                    'amount' => $amount,
                    'user_id' => $user->id
                ]);
                
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
     * Handle Midtrans notification/callback
     */
    public function handleMidtransNotification(array $notification): bool
    {
        try {
            Log::info('Processing Midtrans notification', [
                'order_id' => $notification['order_id'] ?? null,
                'transaction_status' => $notification['transaction_status'] ?? null
            ]);

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

            Log::info('Transaction status updated', [
                'order_id' => $orderId,
                'old_status' => $oldStatus,
                'new_status' => $newStatus->value
            ]);

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

        return DB::transaction(function () use ($wallet, $amount, $bankDetails) {
            $balanceBefore = $wallet->balance;
            $wallet->decrement('balance', $amount);
            
            $transaction = $this->createTransaction($wallet, [
                'type' => WalletTransactionType::WITHDRAW,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->fresh()->balance,
                'description' => 'Penarikan saldo ke ' . $bankDetails['bank_name'] . ' - ' . $bankDetails['account_number'],
                'status' => WalletTransactionStatus::PENDING,
                'reference_id' => $this->generateOrderId('WD', $wallet->user_id),
            ]);
            
            Log::info('Withdraw request created', [
                'transaction_id' => $transaction->id,
                'user_id' => $wallet->user_id,
                'amount' => $amount,
                'bank_details' => $bankDetails
            ]);

            return $transaction;
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
        
        if (!$transaction || $transaction->status !== WalletTransactionStatus::PENDING) {
            throw ValidationException::withMessages([
                'transaction' => ['Transaksi tidak dapat dibatalkan'],
            ]);
        }

        return DB::transaction(function () use ($transaction) {
            $oldStatus = $transaction->status->value;
            $transaction->update(['status' => WalletTransactionStatus::CANCELLED]);
            
            
            event(new PaymentStatusChanged($transaction, $oldStatus, WalletTransactionStatus::CANCELLED->value));
            
            Log::info('Transaction cancelled', [
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->wallet->user_id
            ]);

            return true;
        });
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
            
            Log::info('Top up balance processed successfully', [
                'transaction_id' => $transaction->id,
                'user_id' => $wallet->user_id,
                'amount' => $transaction->amount,
                'new_balance' => $wallet->fresh()->balance
            ]);
        });
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
}
