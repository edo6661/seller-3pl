<?php
namespace App\Events;
use App\Models\PickupRequest;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class PickupRequestCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(
        public PickupRequest $pickupRequest,
        public User $requester
    ) {}
    public function broadcastOn(): array
    {
        return [
            new Channel('user.' . $this->requester->id),
            new Channel('admin-notifications')
        ];
    }
    public function broadcastWith(): array
    {
        return [
            'id' => $this->pickupRequest->id,
            'pickup_code' => $this->pickupRequest->pickup_code,
            'user_name' => $this->requester->name,
            'user_id' => $this->requester->id,
            'total_amount' => $this->pickupRequest->total_amount,
            'status' => $this->pickupRequest->status,
            'delivery_type' => $this->pickupRequest->delivery_type,
            'created_at' => $this->pickupRequest->created_at->toISOString(),
            'notification' => [
                'type' => 'pick_up_request', 
                'title' => 'Pickup Request Baru',
                'message' => "Pickup request {$this->pickupRequest->pickup_code} dari {$this->requester->name} telah dibuat dan menunggu konfirmasi.",
                'icon' => 'fas fa-truck',
                'color' => 'primary',
                'additional_data' => [
                    'pickup_request_id' => $this->pickupRequest->id
                ]
            ]
        ];
    }
    public function broadcastAs(): string
    {
        return 'pickup.created';
    }
}