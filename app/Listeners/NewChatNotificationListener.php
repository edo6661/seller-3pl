<?php
namespace App\Listeners;

use App\Events\NewChatNotification;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NewChatNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(NewChatNotification $event): void
    {
        $message = $event->message;
        $receiver = $event->receiver;

        try {
            // PERBAIKAN: Tambahkan message_id sebagai additional data untuk mencegah duplikasi
            $additionalData = [
                'message_id' => $message->id,
                'conversation_id' => $message->conversation_id
            ];

            // Buat notifikasi untuk receiver dengan pencegahan duplikasi
            $notification = $this->notificationService->createForUser(
                $receiver->id,
                'new_chat_message',
                'Pesan Chat Baru',
                "Pesan baru dari {$message->sender->name}: " . 
                (strlen($message->content) > 50 ? substr($message->content, 0, 50) . '...' : $message->content),
                $additionalData
            );


        } catch (\Exception $e) {
            Log::error('Failed to create chat notification in listener', [
                'message_id' => $message->id,
                'sender_id' => $message->sender_id,
                'receiver_id' => $receiver->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw exception untuk memicu failed() method
            throw $e;
        }
    }

    public function failed(NewChatNotification $event, \Throwable $exception): void
    {
        Log::error('Failed to process NewChatNotification event', [
            'message_id' => $event->message->id,
            'sender_id' => $event->message->sender_id,
            'receiver_id' => $event->receiver->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}