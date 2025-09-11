<?php
// app/Events/PickupRequestCreated.php
namespace App\Events;

use App\Models\PickupRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PickupRequestCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public PickupRequest $pickupRequest
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
        return [
            'id' => $this->pickupRequest->id,
            'pickup_code' => $this->pickupRequest->pickup_code,
            'user_name' => $this->pickupRequest->user->name,
            'total_amount' => $this->pickupRequest->total_amount,
            'status' => $this->pickupRequest->status,
            'created_at' => $this->pickupRequest->created_at->toISOString(),
            'notification' => [
                'type' => 'pickup_created',
                'title' => 'Pickup Request Baru Dibuat',
                'message' => "Pickup request {$this->pickupRequest->pickup_code} telah dibuat dan menunggu konfirmasi.",
                'icon' => 'fas fa-truck',
                'color' => 'primary'
            ]
        ];
    }

    public function broadcastAs(): string
    {
        return 'pickup.created';
    }
}