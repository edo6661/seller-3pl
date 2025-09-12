<?php
namespace App\Listeners;

use App\Enums\UserRole;
use App\Events\SupportTicketReplied;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SupportTicketRepliedListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(SupportTicketReplied $event): void
    {
        $ticket = $event->ticket;
        $response = $event->response;
        $responder = $event->responder;

        try {
            if ($response->is_admin_response) {
                // Admin membalas, kirim notifikasi ke user pemilik ticket
                $this->sendNotificationToUser($ticket, $response, $responder);
            } else {
                // User membalas, kirim notifikasi ke admin
                $this->sendNotificationToAdmins($ticket, $response, $responder);
            }

            Log::info('Support ticket reply notifications sent successfully', [
                'ticket_id' => $ticket->id,
                'response_id' => $response->id,
                'is_admin_response' => $response->is_admin_response
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send support ticket reply notifications', [
                'ticket_id' => $ticket->id,
                'response_id' => $response->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function sendNotificationToUser($ticket, $response, $admin): void
    {
        $this->notificationService->createForUser(
            $ticket->user_id,
            'admin_reply',
            'Admin Membalas Ticket Anda',
            $this->getAdminReplyMessage($ticket, $admin),
            [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'response_id' => $response->id,
                'admin_name' => $admin->name
            ]
        );

        Log::info('Admin reply notification sent to user', [
            'user_id' => $ticket->user_id,
            'ticket_id' => $ticket->id,
            'admin_id' => $admin->id
        ]);
    }

    private function sendNotificationToAdmins($ticket, $response, $user): void
    {
        // Jika ticket sudah di-assign, prioritaskan admin yang di-assign
        if ($ticket->assigned_to) {
            $assignedAdmin = User::find($ticket->assigned_to);
            if ($assignedAdmin) {
                $this->notificationService->createForUser(
                    $assignedAdmin->id,
                    'user_reply',
                    'User Membalas Ticket Yang Di-Assign',
                    $this->getUserReplyMessage($ticket, $user, true),
                    [
                        'ticket_id' => $ticket->id,
                        'ticket_number' => $ticket->ticket_number,
                        'response_id' => $response->id,
                        'user_name' => $user->name,
                        'is_assigned' => true
                    ]
                );

                Log::info('User reply notification sent to assigned admin', [
                    'admin_id' => $assignedAdmin->id,
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id
                ]);
            }
        }

        // Kirim juga ke semua admin lainnya (dengan prioritas lebih rendah)
        $admins = User::where('role', UserRole::ADMIN)
            ->when($ticket->assigned_to, function($query) use ($ticket) {
                return $query->where('id', '!=', $ticket->assigned_to);
            })
            ->get();

        foreach ($admins as $admin) {
            $this->notificationService->createForUser(
                $admin->id,
                'user_reply',
                'User Membalas Ticket',
                $this->getUserReplyMessage($ticket, $user, false),
                [
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'response_id' => $response->id,
                    'user_name' => $user->name,
                    'is_assigned' => false
                ]
            );
        }

        Log::info('User reply notifications sent to all admins', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'admin_count' => $admins->count()
        ]);
    }

    private function getAdminReplyMessage($ticket, $admin): string
    {
        return "Admin {$admin->name} telah membalas ticket #{$ticket->ticket_number} Anda.\n" .
               "Subjek: {$ticket->subject}\n" .
               "Silakan buka aplikasi untuk melihat balasan lengkapnya.";
    }

    private function getUserReplyMessage($ticket, $user, $isAssigned): string
    {
        $assignedText = $isAssigned ? ' (Assigned to you)' : '';
        return "User {$user->name} telah membalas ticket #{$ticket->ticket_number}{$assignedText}.\n" .
               "Subjek: {$ticket->subject}\n" .
               "Prioritas: " . ucfirst($ticket->priority) . "\n" .
               "Status: " . $ticket->getStatusLabel();
    }

    public function failed(SupportTicketReplied $event, \Throwable $exception): void
    {
        Log::error('Failed to process SupportTicketReplied event', [
            'ticket_id' => $event->ticket->id,
            'response_id' => $event->response->id,
            'responder_id' => $event->responder->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
