<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Conversation extends Model
{
    protected $fillable = [
        'seller_id',
        'admin_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'id', 'conversation_id')
                    ->latest('created_at');
    }

    public function unreadMessagesCount(int $userId): int
    {
        return $this->messages()
                    ->where('sender_id', '!=', $userId)
                    ->whereNull('read_at')
                    ->count();
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query->where('admin_id', $user->id);
        }
        
        return $query->where('seller_id', $user->id);
    }

    public function getOtherParticipant(User $user): User
    {
        if ($user->isAdmin()) {
            return $this->seller;
        }
        
        return $this->admin;
    }

    public function markMessagesAsRead(int $userId): void
    {
        $this->messages()
             ->where('sender_id', '!=', $userId)
             ->whereNull('read_at')
             ->update(['read_at' => now()]);
    }
}