<?php
// app/Events/UserTyping.php
namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $conversationId,
        public User $user
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('conversation.' . $this->conversationId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'conversation_id' => $this->conversationId,
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.typing';
    }
}