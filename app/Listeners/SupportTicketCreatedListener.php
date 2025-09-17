<?php
namespace App\Listeners;
use App\Enums\UserRole;
use App\Events\SupportTicketCreated;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
class SupportTicketCreatedListener implements ShouldQueue
{
    use InteractsWithQueue;
    protected NotificationService $notificationService;
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    public function handle(SupportTicketCreated $event): void
    {
        $ticket = $event->ticket;
        $user = $event->user;
        try {
            $admins = User::where('role', UserRole::ADMIN)->get();
            foreach ($admins as $admin) {
                $this->notificationService->createForUser(
                    $admin->id,
                    'support_ticket', 
                    $this->getNotificationTitle($ticket),
                    $this->getNotificationMessage($ticket, $user),
                    [
                        'ticket_id' => $ticket->id,
                        'ticket_number' => $ticket->ticket_number,
                        'priority' => $ticket->priority,
                        'category' => $ticket->category,
                        'ticket_type' => $ticket->ticket_type,
                        'user_id' => $user->id
                    ]
                );
            }
            $this->notificationService->createForUser(
                $user->id,
                'support_ticket', 
                'Ticket Support Berhasil Dibuat',
                "Ticket #{$ticket->ticket_number} telah berhasil dibuat. Tim support akan segera merespons dalam 1x24 jam.",
                [
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to create support ticket notifications', [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    private function getNotificationTitle(SupportTicket $ticket): string
    {
        $priorityText = match($ticket->priority) {
            'urgent' => 'MENDESAK - ',
            'high' => 'PRIORITAS TINGGI - ',
            default => ''
        };
        return $priorityText . 'Ticket Support Baru #' . $ticket->ticket_number;
    }
    private function getNotificationMessage(SupportTicket $ticket, User $user): string
    {
        $categoryText = ucfirst(str_replace('_', ' ', $ticket->category));
        $typeText = $ticket->ticket_type === 'shipment' ? 'Masalah Pengiriman' : 'Masalah Umum';
        return "Ticket #{$ticket->ticket_number} dari {$user->name} membutuhkan perhatian Anda.\n" .
               "Kategori: {$categoryText}\n" .
               "Tipe: {$typeText}\n" .
               "Prioritas: " . ucfirst($ticket->priority) . "\n" .
               "Subjek: {$ticket->subject}";
    }
    public function failed(SupportTicketCreated $event, \Throwable $exception): void
    {
        Log::error('Failed to process SupportTicketCreated event', [
            'ticket_id' => $event->ticket->id,
            'user_id' => $event->user->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
