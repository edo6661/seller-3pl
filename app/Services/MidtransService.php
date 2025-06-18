<?php

namespace App\Services;

use App\Models\WalletTransaction;
use App\Events\PaymentStatusChanged;
use App\Enums\WalletTransactionStatus;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction as MidtransTransaction;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Create transaction in Midtrans
     */
    public function createTransaction(WalletTransaction $transaction, array $payload): WalletTransaction
    {
        try {
            $snapResponse = Snap::createTransaction($payload);
            
            Log::info('Midtrans Transaction Created', [
                'order_id' => $payload['transaction_details']['order_id'],
                'amount' => $payload['transaction_details']['gross_amount'],
                'snap_token' => $snapResponse->token,
                'transaction_id' => $transaction->id
            ]);

            $transaction->update([
                'snap_token' => $snapResponse->token,
                'snap_url' => $snapResponse->redirect_url ?? null,
            ]);
            
            return $transaction->fresh();
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage(), [
                'order_id' => $payload['transaction_details']['order_id'] ?? null,
                'transaction_id' => $transaction->id
            ]);
            throw $e;
        }
    }

    /**
     * Cancel transaction in Midtrans
     */
    public function cancelTransaction(string $orderId): array
    {
        try {
            $cancelResponse = MidtransTransaction::cancel($orderId);
            
            Log::info('Midtrans Transaction Cancelled', [
                'order_id' => $orderId,
                'status' => $cancelResponse->transaction_status ?? 'cancelled'
            ]);
            
            return [
                'success' => true,
                'data' => json_decode(json_encode($cancelResponse), true)
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans Cancel Error: ' . $e->getMessage(), [
                'order_id' => $orderId
            ]);
            
            // Jika error 404 atau transaksi sudah expired/cancelled, anggap berhasil
            if (strpos($e->getMessage(), '404') !== false || 
                strpos($e->getMessage(), 'Transaction doesn\'t exist') !== false ||
                strpos($e->getMessage(), 'Transaction status cannot be updated') !== false) {
                return [
                    'success' => true,
                    'data' => ['transaction_status' => 'cancel']
                ];
            }
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get transaction status from Midtrans
     */
    public function getStatus(string $orderId): array
    {
        try {
            $status = MidtransTransaction::status($orderId);
            Log::info('Midtrans Status Retrieved', [
                'order_id' => $orderId,
                'status' => $status
            ]);
            return [
                'success' => true,
                'data' => json_decode(json_encode($status), true)
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans Status Error: ' . $e->getMessage(), [
                'order_id' => $orderId
            ]);
            throw $e;
        }
    }
    
    /**
     * Handle Midtrans notification and update transaction
     */
    public function handleNotification(array $notificationData): WalletTransaction
    {
        try {
            $orderId = $notificationData['order_id'];
            $transaction = WalletTransaction::where('reference_id', $orderId)->firstOrFail();
            
            $oldStatus = $transaction->status->value;
            $newStatus = $this->mapMidtransStatus($notificationData['transaction_status']);
            
            $transaction->update([
                'payment_type' => $notificationData['payment_type'] ?? null,
                'status' => $newStatus,
            ]);
            
            if ($oldStatus !== $newStatus->value) {
                event(new PaymentStatusChanged($transaction, $oldStatus, $newStatus->value));
            }
            
            return $transaction->fresh();
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage(), [
                'notification_data' => $notificationData
            ]);
            throw $e;
        }
    }
    
    /**
     * Map Midtrans status to internal status
     */
    private function mapMidtransStatus(string $midtransStatus): WalletTransactionStatus
    {
        return match($midtransStatus) {
            'settlement', 'capture' => WalletTransactionStatus::SUCCESS,
            'pending' => WalletTransactionStatus::PENDING,
            'expire', 'deny' => WalletTransactionStatus::FAILED,
            'cancel' => WalletTransactionStatus::CANCELLED,
            default => WalletTransactionStatus::FAILED,
        };
    }
}