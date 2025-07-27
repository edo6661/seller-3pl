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
class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(
        public Message $message
    ) {}
    public function broadcastOn(): array
    {
        $channels = [
            new Channel('conversation.' . $this->message->conversation_id),
        ];
        $conversation = $this->message->conversation;
        if ($conversation->seller_id !== $this->message->sender_id) {
            $channels[] = new Channel('user.' . $conversation->seller_id);
        }
        if ($conversation->admin_id !== $this->message->sender_id) {
            $channels[] = new Channel('user.' . $conversation->admin_id);
        }
        return $channels;
    }
    public function broadcastWith(): array
    {
        $conversation = $this->message->conversation;
        $receiverId = $conversation->seller_id === $this->message->sender_id 
            ? $conversation->admin_id 
            : $conversation->seller_id;
        $totalUnread = User::find($receiverId)?->getTotalUnreadMessages() ?? 0;
        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'content' => $this->message->content,
            'created_at' => $this->message->created_at->toISOString(),
            'total_unread' => $totalUnread,
        ];
    }
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}