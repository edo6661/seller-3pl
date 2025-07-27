<?php

namespace App\Listeners;

use App\Events\MessageSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class MessageSentListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }

    public function handle(MessageSent $event): void
    {
        $message = $event->message;
        $conversation = $message->conversation;
        
        // Log aktivitas chat untuk audit
        Log::info('New message sent', [
            'message_id' => $message->id,
            'conversation_id' => $conversation->id,
            'sender_id' => $message->sender_id,
            'sender_role' => $message->sender->role->value,
            'created_at' => $message->created_at,
        ]);

        // Di sini bisa ditambahkan logic lain seperti:
        // - Mengirim email notification jika diperlukan
        // - Update last activity user
        // - Analytics tracking
        // - Push notification (jika diperlukan nanti)
    }

    public function failed(MessageSent $event, \Throwable $exception): void
    {
        Log::error('Failed to process MessageSent event', [
            'message_id' => $event->message->id,
            'error' => $exception->getMessage(),
        ]);
    }
}