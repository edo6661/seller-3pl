<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupRequest extends Model
{
    /** @use HasFactory<\Database\Factories\PickupRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'pickup_code',
        'user_id',
        'recipient_name',
        'recipient_phone',
        'recipient_city',
        'recipient_province',
        'recipient_postal_code',
        'recipient_address',
        'recipient_latitude',
        'recipient_longitude',
        'pickup_name',
        'pickup_phone',
        'pickup_city',
        'pickup_province',
        'pickup_postal_code',
        'pickup_address',
        'pickup_latitude',
        'pickup_longitude',
        'pickup_scheduled_at',
        'payment_method',
        'shipping_cost',
        'service_fee',
        'product_total',
        'cod_amount',
        'total_amount',
        'status',
        'courier_service',
        'courier_tracking_number',
        'courier_response',
        'notes',
        'requested_at',
        'picked_up_at',
        'delivered_at',
        'cod_collected_at'
    ];

    protected $casts = [
        'pickup_latitude' => 'decimal:8',
        'pickup_longitude' => 'decimal:8',
        'recipient_latitude' => 'decimal:8',
        'recipient_longitude' => 'decimal:8',
        'shipping_cost' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'product_total' => 'decimal:2',
        'cod_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'courier_response' => 'array',
        'requested_at' => 'datetime',
        'pickup_scheduled_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cod_collected_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PickupRequestItem::class);
    }

    public function buyerRating()
    {
        return $this->belongsTo(BuyerRating::class, 'recipient_phone', 'phone_number');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePickupScheduled($query)
    {
        return $query->where('status', 'pickup_scheduled');
    }

    public function scopePickedUp($query)
    {
        return $query->where('status', 'picked_up');
    }

    public function scopeInTransit($query)
    {
        return $query->where('status', 'in_transit');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeBalancePayment($query)
    {
        return $query->where('payment_method', 'balance');
    }

    public function scopeWalletPayment($query)
    {
        return $query->where('payment_method', 'wallet');
    }

    // Helpers
    public static function generatePickupCode()
    {
        return 'PU' . now()->format('ymd') . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }

    public function isBalancePayment()
    {
        return $this->payment_method === 'balance';
    }

    public function isWalletPayment()
    {
        return $this->payment_method === 'wallet';
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function isCompleted()
    {
        return $this->status === 'delivered';
    }

    public function isFailed()
    {
        return in_array($this->status, ['failed', 'cancelled']);
    }

    public function getFullRecipientAddressAttribute()
    {
        return "{$this->recipient_address}, {$this->recipient_city}, {$this->recipient_province} {$this->recipient_postal_code}";
    }

    public function getFullPickupAddressAttribute()
    {
        return "{$this->pickup_address}, {$this->pickup_city}, {$this->pickup_province} {$this->pickup_postal_code}";
    }

    public function updateBuyerRating()
    {
        $buyerRating = BuyerRating::findOrCreateByPhone($this->recipient_phone, $this->recipient_name);
        
        $isSuccessful = $this->status === 'delivered';
        $isCancelled = $this->status === 'cancelled';
        $isFailed = $this->status === 'failed';
        
        $buyerRating->updateStats($isSuccessful, $isCancelled, $isFailed);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pickupRequest) {
            $pickupRequest->pickup_code = static::generatePickupCode();
            $pickupRequest->requested_at = now();
        });

        static::updated(function ($pickupRequest) {
            // Update buyer rating when status changes
            if ($pickupRequest->isDirty('status') && in_array($pickupRequest->status, ['delivered', 'failed', 'cancelled'])) {
                $pickupRequest->updateBuyerRating();
            }
        });
    }
}