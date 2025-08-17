<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketResponse extends Model
{
    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'message',
        'attachments',
        'is_admin_response',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_admin_response' => 'boolean',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Relations
    public function supportTicket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeAdminResponses($query)
    {
        return $query->where('is_admin_response', true);
    }

    public function scopeUserResponses($query)
    {
        return $query->where('is_admin_response', false);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // Methods
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function isFromAdmin(): bool
    {
        return $this->is_admin_response;
    }

    public function isFromUser(): bool
    {
        return !$this->is_admin_response;
    }
}