<?php
namespace App\Events;
use App\Models\PickupRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class PickupRequestStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(
        public PickupRequest $pickupRequest,
        public string $oldStatus
    ) {}
    public function broadcastOn(): array
    {
        return [
            new Channel('admin-notifications'),
            new Channel('user.' . $this->pickupRequest->user_id),
        ];
    }
    public function broadcastWith(): array
    {
        $notifications = [
            'confirmed' => [
                'title' => 'Pickup Request Dikonfirmasi',
                'message' => "Pickup request {$this->pickupRequest->pickup_code} telah dikonfirmasi.",
                'icon' => 'fas fa-check-circle',
                'color' => 'success'
            ],
            'pickup_scheduled' => [
                'title' => 'Pickup Dijadwalkan',
                'message' => "Pickup request {$this->pickupRequest->pickup_code} telah dijadwalkan.",
                'icon' => 'fas fa-calendar',
                'color' => 'info'
            ],
            'picked_up' => [
                'title' => 'Paket Telah Diambil',
                'message' => "Paket {$this->pickupRequest->pickup_code} telah diambil kurir.",
                'icon' => 'fas fa-shipping-fast',
                'color' => 'warning'
            ],
            'delivered' => [
                'title' => 'Paket Telah Diterima',
                'message' => "Paket {$this->pickupRequest->pickup_code} telah berhasil diterima.",
                'icon' => 'fas fa-check-double',
                'color' => 'success'
            ],
            'cancelled' => [
                'title' => 'Pickup Request Dibatalkan',
                'message' => "Pickup request {$this->pickupRequest->pickup_code} telah dibatalkan.",
                'icon' => 'fas fa-times-circle',
                'color' => 'error'
            ],
        ];
        $notification = $notifications[$this->pickupRequest->status] ?? [
            'title' => 'Status Pickup Request Diupdate',
            'message' => "Status pickup request {$this->pickupRequest->pickup_code} telah diupdate.",
            'icon' => 'fas fa-info-circle',
            'color' => 'info'
        ];
        return [
            'id' => $this->pickupRequest->id,
            'pickup_code' => $this->pickupRequest->pickup_code,
            'user_name' => $this->pickupRequest->user->name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->pickupRequest->status,
            'updated_at' => $this->pickupRequest->updated_at->toISOString(),
            'notification' => array_merge($notification, [
                'type' => 'pick_up_request', 
                'additional_data' => [
                    'pickup_request_id' => $this->pickupRequest->id,
                    'pickup_code' => $this->pickupRequest->pickup_code,
                    'status' => $this->pickupRequest->status
                ]
            ])
        ];
    }
    public function broadcastAs(): string
    {
        return 'pickup.status.updated';
    }
}
