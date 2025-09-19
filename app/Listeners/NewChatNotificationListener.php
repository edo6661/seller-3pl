<?php
namespace App\Listeners;
use App\Events\NewChatNotification;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class NewChatNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;
    protected NotificationService $notificationService;
    public $tries = 3;
    public $maxExceptions = 2;
    public $backoff = [10, 30, 60]; 
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    public function handle(NewChatNotification $event): void
    {
        $message = $event->message;
        $receiver = $event->receiver;
        try {
            DB::transaction(function () use ($message, $receiver) {
                if (!$message || !$receiver || !$message->id || !$receiver->id) {
                    throw new \InvalidArgumentException('Invalid message or receiver data');
                }
                $message->load('sender');
                if (!$message->sender) {
                    throw new \Exception('Message sender not found');
                }
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
                Log::info('Chat notification created successfully', [
                    'notification_id' => $notification->id,
                    'message_id' => $message->id,
                    'receiver_id' => $receiver->id
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Failed to create chat notification in listener', [
                'message_id' => $message?->id,
                'sender_id' => $message?->sender_id,
                'receiver_id' => $receiver?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'attempt' => $this->attempts()
            ]);
            throw $e;
        }
    }
    /**
     * Handle failed job
     */
    public function failed(NewChatNotification $event, \Throwable $exception): void
    {
        Log::error('NewChatNotificationListener failed after all retries', [
            'message_id' => $event->message?->id,
            'receiver_id' => $event->receiver?->id,
            'error' => $exception->getMessage(),
            'final_attempt' => true
        ]);
    }
}