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
                $this->notificationService->createForUser(
                    $admin->id,
                    'pick_up_request',
                    'Pickup Request Baru Perlu Konfirmasi',
                    "Pickup request {$pickupRequest->pickup_code} dari {$requester->name} telah dibuat dan membutuhkan konfirmasi Anda. Total: Rp " . number_format($pickupRequest->total_amount, 0, ',', '.'),
                    [
                        'pickup_request_id' => $pickupRequest->id,
                        'pickup_code' => $pickupRequest->pickup_code,
                        'requester_id' => $requester->id
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to create pickup request notifications', [
                'pickup_request_id' => $pickupRequest->id,
                'requester_id' => $requester->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}