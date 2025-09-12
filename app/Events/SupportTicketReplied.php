<?php

// app/Events/SupportTicketReplied.php
namespace App\Events;

use App\Models\SupportTicket;
use App\Models\TicketResponse;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupportTicketReplied implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public SupportTicket $ticket,
        public TicketResponse $response,
        public User $responder
    ) {}

    public function broadcastOn(): array
    {
        $channels = [];

        // Jika yang reply adalah admin, kirim notifikasi ke user pemilik ticket
        if ($this->response->is_admin_response) {
            $channels[] = new Channel('user.' . $this->ticket->user_id);
        } else {
            // Jika yang reply adalah user, kirim notifikasi ke semua admin
            $channels[] = new Channel('admin-notifications');
            $channels[] = new Channel('admin-global');
            
            // Jika ticket sudah di-assign ke admin tertentu, kirim ke admin tersebut juga
            if ($this->ticket->assigned_to) {
                $channels[] = new Channel('user.' . $this->ticket->assigned_to);
            }
        }

        return $channels;
    }

    public function broadcastWith(): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'response_id' => $this->response->id,
            'responder_name' => $this->responder->name,
            'responder_id' => $this->responder->id,
            'is_admin_response' => $this->response->is_admin_response,
            'message_preview' => substr(strip_tags($this->response->message), 0, 100) . '...',
            'created_at' => $this->response->created_at->toISOString(),
            'notification' => [
                'type' => $this->response->is_admin_response ? 'admin_reply' : 'user_reply',
                'title' => $this->response->is_admin_response 
                    ? 'Admin Membalas Ticket Anda'
                    : 'User Membalas Ticket',
                'message' => $this->response->is_admin_response
                    ? "Admin telah membalas ticket #{$this->ticket->ticket_number}. Silakan cek untuk melihat balasan."
                    : "User {$this->responder->name} telah membalas ticket #{$this->ticket->ticket_number}.",
                'icon' => 'fas fa-reply',
                'color' => $this->response->is_admin_response ? 'info' : 'warning'
            ]
        ];
    }

    public function broadcastAs(): string
    {
        return 'support.ticket.replied';
    }
}
