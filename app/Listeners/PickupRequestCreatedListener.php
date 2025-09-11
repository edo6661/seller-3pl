<?php

// app/Listeners/PickupRequestCreatedListener.php
namespace App\Listeners;

use App\Events\PickupRequestCreated;
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
        
        // Buat notifikasi untuk user yang membuat request
        $this->notificationService->createForUser(
            $pickupRequest->user_id,
            'pickup_created',
            'Pickup Request Berhasil Dibuat',
            "Pickup request {$pickupRequest->pickup_code} telah berhasil dibuat dan sedang menunggu konfirmasi admin."
        );

        // Buat notifikasi untuk semua admin
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $this->notificationService->createForUser(
                $admin->id,
                'pickup_created',
                'Pickup Request Baru',
                "Pickup request baru {$pickupRequest->pickup_code} dari {$pickupRequest->user->name} perlu dikonfirmasi."
            );
        }

    }

    public function failed(PickupRequestCreated $event, \Throwable $exception): void
    {
        Log::error('Failed to process PickupRequestCreated event', [
            'pickup_request_id' => $event->pickupRequest->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
