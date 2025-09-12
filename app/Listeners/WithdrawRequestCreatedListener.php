<?php
namespace App\Listeners;

use App\Enums\UserRole;
use App\Events\WithdrawRequestCreated;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class WithdrawRequestCreatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(WithdrawRequestCreated $event): void
    {
        $transaction = $event->transaction;
        $user = $event->user;

        try {
            $admins = User::where('role', UserRole::ADMIN)->get();
            
            foreach ($admins as $admin) {
                Log::info('Creating withdraw notification for admin', [
                    'admin_id' => $admin->id,
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id
                ]);

                $this->notificationService->createForUser(
                    $admin->id,
                    'withdraw_request_created',
                    'Permintaan Penarikan Baru',
                    $this->getAdminNotificationMessage($transaction, $user),
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to create withdraw notifications', [
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
        return "Permintaan penarikan baru dari {$user->name}.\n" .
               "Jumlah: {$transaction->formatted_amount}\n" .
               "Biaya Admin: {$transaction->formatted_admin_fee}\n" .
               "Jumlah Bersih: {$transaction->formatted_net_amount}\n" .
               "Bank: {$transaction->bank_name} - {$transaction->account_number}\n" .
               "ID Transaksi: {$transaction->reference_id}";
    }

    public function failed(WithdrawRequestCreated $event, \Throwable $exception): void
    {
        Log::error('Failed to process WithdrawRequestCreated event', [
            'transaction_id' => $event->transaction->id,
            'user_id' => $event->user->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
