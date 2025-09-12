<?php
namespace App\Listeners;
use App\Enums\UserRole;
use App\Events\TopUpTransactionCreated;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
class TopUpTransactionCreatedListener implements ShouldQueue
{
    use InteractsWithQueue;
    protected NotificationService $notificationService;
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    public function handle(TopUpTransactionCreated $event): void
    {
        $transaction = $event->transaction;
        $user = $event->user;
        try {
            $admins = User::where('role', UserRole::ADMIN)->get();
            foreach ($admins as $admin) {
                Log::info('Creating topup notification for admin', [
                    'admin_id' => $admin->id,
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id
                ]);
                $this->notificationService->createForUser(
                    $admin->id,
                    'topup_transaction_created',
                    'Permintaan Top Up Baru',
                    $this->getAdminNotificationMessage($transaction, $user),
                   
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to create topup notifications', [
                'transaction_id' => $transaction->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    private function getAdminNotificationMessage($transaction, $user): string
    {
        return "Permintaan top up baru dari {$user->name}.\n" .
               "Jumlah: {$transaction->formatted_amount}\n" .
               "ID Transaksi: {$transaction->reference_id}\n" .
               "Status: " . $transaction->status->label();
    }
    public function failed(TopUpTransactionCreated $event, \Throwable $exception): void
    {
        Log::error('Failed to process TopUpTransactionCreated event', [
            'transaction_id' => $event->transaction->id,
            'user_id' => $event->user->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
