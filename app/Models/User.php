<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

   
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
    ];

    
    public function sellerProfile()
    {
        return $this->hasOne(SellerProfile::class);
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
        return $this->role === 'seller';
    }

    public function isProfileComplete()
    {
        return $this->sellerProfile && $this->sellerProfile->is_profile_complete;
    }
}
