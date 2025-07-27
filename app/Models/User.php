<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

   
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'avatar',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class, 

    ];

    public function isEmailVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }
    
    public function sellerProfile()
    {
        return $this->hasOne(SellerProfile::class);
    }
    public function hasSellerProfile(): bool
    {
        return $this->sellerProfile()->exists();
    }
    public function hasCompleteSellerProfile(): bool
    {
        return $this->sellerProfile()->complete()->exists();
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function pickupRequests()
    {
        return $this->hasMany(PickupRequest::class);
    }

    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawRequest::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    
    public function scopeSellers($query)
    {
        return $query->where('role', 'seller');
    }

    
    
    public function isSeller()
    {
        return $this->role === UserRole::SELLER;
    }
    public function isAdmin()
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isProfileComplete()
    {
        return $this->sellerProfile && $this->sellerProfile->is_profile_complete;
    }
    public function getRoleLabelAttribute(): string
    {
        return $this->role->label();
    }

    public function sellerConversations()
    {
        return $this->hasMany(Conversation::class, 'seller_id');
    }

    public function adminConversations()
    {
        return $this->hasMany(Conversation::class, 'admin_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function getConversationWith(User $otherUser): ?Conversation
    {
        if ($this->isAdmin() && $otherUser->isSeller()) {
            return Conversation::where('admin_id', $this->id)
                            ->where('seller_id', $otherUser->id)
                            ->first();
        }
        
        if ($this->isSeller() && $otherUser->isAdmin()) {
            return Conversation::where('seller_id', $this->id)
                            ->where('admin_id', $otherUser->id)
                            ->first();
        }
        
        return null;
    }

    public function getTotalUnreadMessages(): int
    {
        $conversationsQuery = $this->isAdmin() 
            ? $this->adminConversations() 
            : $this->sellerConversations();
            
        return $conversationsQuery->get()->sum(function ($conversation) {
            return $conversation->unreadMessagesCount($this->id);
        });
    }
}
