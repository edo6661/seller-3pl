<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerProfile extends Model
{
        protected $fillable = [
        'user_id',
        'business_name',
        'address',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'is_profile_complete'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_profile_complete' => 'boolean'
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function getFullAddressAttribute()
    {
        return "{$this->address}, {$this->city}, {$this->province} {$this->postal_code}";
    }

    public function hasCoordinates()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($profile) {
            
            $profile->is_profile_complete = !empty($profile->address) 
                && !empty($profile->city) 
                && !empty($profile->province) 
                && !empty($profile->postal_code);
        });
    }

}
