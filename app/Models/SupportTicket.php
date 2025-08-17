<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $fillable = [
        'ticket_number',
        'user_id',
        'ticket_type',
        'pickup_request_id',
        'tracking_number',
        'category',
        'subject',
        'description',
        'priority',
        'status',
        'assigned_to',
        'attachments',
        'admin_notes',
        'resolution',
        'resolved_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'resolved_at' => 'datetime',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function pickupRequest(): BelongsTo
    {
        return $this->belongsTo(PickupRequest::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(TicketResponse::class)->orderBy('created_at', 'asc');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeGeneral($query)
    {
        return $query->where('ticket_type', 'general');
    }

    public function scopeShipment($query)
    {
        return $query->where('ticket_type', 'shipment');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    // Methods
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isShipmentType(): bool
    {
        return $this->ticket_type === 'shipment';
    }

    public function isGeneralType(): bool
    {
        return $this->ticket_type === 'general';
    }

    public function canBeReopened(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    public function canBeResolved(): bool
    {
        return !in_array($this->status, ['resolved', 'closed']);
    }

    public function markAsResolved(string $resolution = null): void
    {
        $this->update([
            'status' => 'resolved',
            'resolution' => $resolution,
            'resolved_at' => now(),
        ]);
    }

    public function getReferenceNumber(): ?string
    {
        if ($this->isShipmentType()) {
            return $this->tracking_number ?: $this->pickupRequest?->pickup_code;
        }
        return null;
    }

    public function getCategoryLabel(): string
    {
        return match ($this->category) {
            'delivery_issue' => 'Masalah Pengiriman',
            'payment_issue' => 'Masalah Pembayaran',
            'item_damage' => 'Barang Rusak',
            'item_lost' => 'Barang Hilang',
            'wrong_address' => 'Alamat Salah',
            'courier_service' => 'Masalah Kurir',
            'app_technical' => 'Masalah Teknis Aplikasi',
            'account_issue' => 'Masalah Akun',
            'other' => 'Lainnya',
            default => ucfirst($this->category),
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'open' => 'Terbuka',
            'in_progress' => 'Dalam Proses',
            'waiting_user' => 'Menunggu Respon',
            'resolved' => 'Diselesaikan',
            'closed' => 'Ditutup',
            default => ucfirst($this->status),
        };
    }

    public function getPriorityLabel(): string
    {
        return match ($this->priority) {
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            'urgent' => 'Mendesak',
            default => ucfirst($this->priority),
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'open' => 'bg-blue-100 text-blue-800',
            'in_progress' => 'bg-yellow-100 text-yellow-800',
            'waiting_user' => 'bg-orange-100 text-orange-800',
            'resolved' => 'bg-green-100 text-green-800',
            'closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPriorityBadgeClass(): string
    {
        return match ($this->priority) {
            'low' => 'bg-gray-100 text-gray-800',
            'medium' => 'bg-blue-100 text-blue-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Generate ticket number
    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT';
        $date = now()->format('ymd');
        $sequence = static::whereDate('created_at', today())->count() + 1;
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            $ticket->ticket_number = static::generateTicketNumber();
        });
    }
}