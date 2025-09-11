<?php
namespace App\Events;
use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(
        public Message $message
    ) {}
    public function broadcastOn(): array
    {
        $conversation = $this->message->conversation;
        $channels = [];
        $channels[] = new Channel('conversation.' . $this->message->conversation_id);
        $receiverId = $conversation->seller_id === $this->message->sender_id 
            ? $conversation->admin_id 
            : $conversation->seller_id;
        $channels[] = new Channel('user.' . $receiverId);
        if ($this->message->sender->isSeller()) {
            $channels[] = new Channel('admin-notifications');
            $channels[] = new Channel('admin-global');
        }
        return $channels;
    }
    public function broadcastWith(): array
    {
        $conversation = $this->message->conversation;
        $receiverId = $conversation->seller_id === $this->message->sender_id 
            ? $conversation->admin_id 
            : $conversation->seller_id;
        $receiver = User::find($receiverId);
        $totalUnread = $receiver ? $receiver->getTotalUnreadMessages() : 0;
        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'sender_role' => $this->message->sender->role->value,
            'content' => $this->message->content,
            'created_at' => $this->message->created_at->toISOString(),
            'total_unread' => $totalUnread,
            'receiver_id' => $receiverId,
        ];
    }
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}