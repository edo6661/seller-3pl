<?php
namespace App\Listeners;
use App\Events\MessageSent;
use App\Events\NewMessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
class MessageSentListener implements ShouldQueue
{
    use InteractsWithQueue;
    public function __construct()
    {
    }
    public function handle(MessageSent $event): void
    {
        $message = $event->message;
        $conversation = $message->conversation;
        $receiver = $conversation->seller_id === $message->sender_id 
            ? $conversation->admin 
            : $conversation->seller;
        if ($receiver) {
            event(new NewMessageNotification($message, $receiver));
        }
    }
    public function failed(MessageSent $event, \Throwable $exception): void
    {
        Log::error('Failed to process MessageSent event', [
            'message_id' => $event->message->id,
            'error' => $exception->getMessage(),
        ]);
    }
}