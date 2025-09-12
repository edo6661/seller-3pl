<?php
namespace App\Listeners;

use App\Events\TransactionStatusChanged;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class TransactionStatusChangedListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(TransactionStatusChanged $event): void
    {
        $transaction = $event->transaction;
        $user = $event->user;
        $action = $event->action;

        try {
            $notificationData = $this->getNotificationData($transaction, $action);
            
            $this->notificationService->createForUser(
                $user->id,
                $notificationData['type'],
                $notificationData['title'],
                $notificationData['message'],
            );

            Log::info('Transaction status change notification sent', [
                'transaction_id' => $transaction->id,
                'user_id' => $user->id,
                'action' => $action,
                'old_status' => $event->oldStatus,
                'new_status' => $event->newStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send transaction status change notification', [
                'transaction_id' => $transaction->id,
                'user_id' => $user->id,
                'action' => $action,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function getNotificationData($transaction, $action): array
    {
        $isTopUp = $transaction->type->value === 'topup';
        $transactionType = $isTopUp ? 'Top Up' : 'Penarikan';

        return match($action) {
            'approve' => [
                'type' => $isTopUp ? 'topup_approved' : 'withdraw_approved',
                'title' => "{$transactionType} Disetujui",
                'message' => $isTopUp 
                    ? "Top Up sebesar {$transaction->formatted_amount} telah berhasil disetujui dan saldo Anda telah ditambahkan."
                    : "Penarikan sebesar {$transaction->formatted_net_amount} telah disetujui dan akan segera ditransfer ke rekening Anda."
            ],
            'reject' => [
                'type' => $isTopUp ? 'topup_rejected' : 'withdraw_rejected',
                'title' => "{$transactionType} Ditolak",
                'message' => $isTopUp
                    ? "Top Up sebesar {$transaction->formatted_amount} ditolak oleh admin. Alasan: " . ($transaction->admin_notes ?? 'Tidak ada keterangan')
                    : "Penarikan sebesar {$transaction->formatted_amount} ditolak. Saldo telah dikembalikan. Alasan: " . ($transaction->admin_notes ?? 'Tidak ada keterangan')
            ],
            'process' => [
                'type' => 'transaction_processing',
                'title' => "{$transactionType} Sedang Diproses",
                'message' => $isTopUp
                    ? "Top Up sebesar {$transaction->formatted_amount} sedang diproses oleh admin."
                    : "Penarikan sebesar {$transaction->formatted_net_amount} sedang diproses dan akan segera ditransfer."
            ],
            'cancel' => [
                'type' => 'transaction_cancelled',
                'title' => "{$transactionType} Dibatalkan",
                'message' => $isTopUp
                    ? "Top Up sebesar {$transaction->formatted_amount} telah dibatalkan."
                    : "Penarikan sebesar {$transaction->formatted_amount} telah dibatalkan dan saldo dikembalikan ke akun Anda."
            ],
            default => [
                'type' => 'transaction_updated',
                'title' => 'Status Transaksi Diperbarui',
                'message' => "Status transaksi {$transactionType} sebesar {$transaction->formatted_amount} telah diperbarui."
            ]
        };
    }

    public function failed(TransactionStatusChanged $event, \Throwable $exception): void
    {
        Log::error('Failed to process TransactionStatusChanged event', [
            'transaction_id' => $event->transaction->id,
            'user_id' => $event->user->id,
            'action' => $event->action,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}