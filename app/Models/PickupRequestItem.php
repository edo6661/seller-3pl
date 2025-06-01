<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pickup_request_id', 'product_id', 'quantity',
        'weight_per_pcs', 'price_per_pcs', 'total_weight', 'total_price'
    ];

    protected $casts = [
        'weight_per_pcs' => 'decimal:2',
        'price_per_pcs' => 'decimal:2',
        'total_weight' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            $model->total_weight = $model->quantity * $model->weight_per_pcs;
            $model->total_price = $model->quantity * $model->price_per_pcs;
        });
    }

    public function pickupRequest()
    {
        return $this->belongsTo(PickupRequest::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
