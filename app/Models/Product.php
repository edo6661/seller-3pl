<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
        protected $fillable = [
        'user_id', 'name', 'description', 
        'weight_per_pcs', 'price', 'is_active'
    ];

    protected $casts = [
        'weight_per_pcs' => 'decimal:2',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pickupRequestItems()
    {
        return $this->hasMany(PickupRequestItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}
