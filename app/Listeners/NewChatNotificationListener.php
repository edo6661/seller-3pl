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
            $additionalData = [
                'message_id' => $message->id,
                'conversation_id' => $message->conversation_id
            ];

            $notification = $this->notificationService->createForUser(
                $receiver->id,
                'chat', 
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
            throw $e;
        }
    }
}
