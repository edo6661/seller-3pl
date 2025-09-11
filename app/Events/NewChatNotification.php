<?php
namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Message $message,
        public User $receiver
    ) {}

    public function broadcastOn(): array
    {
        $channels = [
            new Channel('user.' . $this->receiver->id),
        ];

        // Jika receiver adalah admin, broadcast juga ke admin channel
        if ($this->receiver->isAdmin()) {
            $channels[] = new Channel('admin-notifications');
        }

        return $channels;
    }

    public function broadcastWith(): array
    {
        return [
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_name' => $this->message->sender->name,
            'sender_role' => $this->message->sender->role->value,
            'content_preview' => substr($this->message->content, 0, 100),
            'created_at' => $this->message->created_at->toISOString(),
            'notification' => [
                'type' => 'new_chat',
                'title' => 'Pesan Chat Baru',
                'message' => "Pesan baru dari {$this->message->sender->name}: " . substr($this->message->content, 0, 50) . (strlen($this->message->content) > 50 ? '...' : ''),
                'icon' => 'fas fa-comments',
                'color' => 'info'
            ]
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.notification';
    }
}