<?php
// app/Listeners/PickupRequestCreatedListener.php
namespace App\Listeners;

use App\Enums\UserRole;
use App\Events\PickupRequestCreated;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class PickupRequestCreatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(PickupRequestCreated $event): void
    {
        $pickupRequest = $event->pickupRequest;
        $requester = $event->requester;

        try {
            $admins = User::where('role', UserRole::ADMIN)->get();
            foreach ($admins as $admin) {
                Log::info('Creating pickup request notification for admin', [
                    'admin_id' => $admin->id,
                    'pickup_request_id' => $pickupRequest->id,
                    'requester_id' => $requester->id
                ]);
                $this->notificationService->createForUser(
                    $admin->id,
                    'pickup_created',
                    'Pickup Request Baru Perlu Konfirmasi',
                    "Pickup request {$pickupRequest->pickup_code} dari {$requester->name} telah dibuat dan membutuhkan konfirmasi Anda. Total: Rp " . number_format($pickupRequest->total_amount, 0, ',', '.'),
                    
                );
            }

            Log::info('Pickup request notifications created for admins only', [
                'pickup_request_id' => $pickupRequest->id,
                'requester_id' => $requester->id,
                'admin_count' => $admins->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create pickup request notifications', [
                'pickup_request_id' => $pickupRequest->id,
                'requester_id' => $requester->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    public function failed(PickupRequestCreated $event, \Throwable $exception): void
    {
        Log::error('Failed to process PickupRequestCreated event', [
            'pickup_request_id' => $event->pickupRequest->id,
            'requester_id' => $event->requester->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}