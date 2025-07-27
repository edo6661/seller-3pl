<?php

// Buat file baru: app/Events/NewMessageNotification.php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Message $message,
        public User $receiver
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('user.' . $this->receiver->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'content' => $this->message->content,
            'created_at' => $this->message->created_at->toISOString(),
            'total_unread' => $this->receiver->getTotalUnreadMessages(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new.message';
    }
}