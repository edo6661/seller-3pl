<?php
namespace App\Events;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class SupportTicketCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(
        public SupportTicket $ticket,
        public User $user
    ) {}
    public function broadcastOn(): array
    {
        return [
            new Channel('admin-notifications'),
            new Channel('admin-global'),
            new Channel('user.' . $this->user->id)
        ];
    }
    public function broadcastWith(): array
    {
        return [
            'id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'user_name' => $this->user->name,
            'user_id' => $this->user->id,
            'subject' => $this->ticket->subject,
            'category' => $this->ticket->category,
            'priority' => $this->ticket->priority,
            'ticket_type' => $this->ticket->ticket_type,
            'created_at' => $this->ticket->created_at->toISOString(),
            'notification' => [
                'type' => 'support_ticket', 
                'title' => 'Support Ticket Baru',
                'message' => "Ticket #{$this->ticket->ticket_number} dari {$this->user->name} membutuhkan perhatian Anda.",
                'icon' => 'fas fa-ticket-alt',
                'color' => $this->getPriorityColor($this->ticket->priority)
            ]
        ];
    }
    public function broadcastAs(): string
    {
        return 'support.ticket.created';
    }
    private function getPriorityColor(string $priority): string
    {
        return match($priority) {
            'urgent' => 'error',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'success',
            default => 'info'
        };
    }
}