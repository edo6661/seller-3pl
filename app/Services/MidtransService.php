<?php
namespace App\Services;

use App\Enums\WalletTransactionStatus;
use App\Events\PaymentStatusChanged;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;
use Midtrans\Transaction as MidtransTransaction;

class MidtransService
{
    /**
     * Membuat transaksi dan mendapatkan snap token
     */
    public function createTransaction(WalletTransaction $transaction, array $payload): WalletTransaction
    {
        try {
            $snapResponse = Snap::createTransaction($payload);
            
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
     * Get transaction status from Midtrans
     */
    public function getStatus(string $orderId): array
    {
        try {
            $status = MidtransTransaction::status($orderId);
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