<?php
namespace App\Models;
use App\Enums\DeliveryType;
use Illuminate\Database\Eloquent\Model;
class PickupRequest extends Model
{
    protected $fillable = [
        'pickup_code',
        'user_id',
        'delivery_type',
        'address_id', 
        'recipient_name',
        'recipient_phone',
        'recipient_city',
        'recipient_province',
        'recipient_postal_code',
        'recipient_address',
        'recipient_latitude',
        'recipient_longitude',
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
        'pickup_scheduled_at',
        'picked_up_at',
        'delivered_at',
        'cod_collected_at',
    ];
    protected $casts = [
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
        'delivery_type' => DeliveryType::class,
    ];
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
    public function pickupAddress()
    {
        return $this->belongsTo(UserAddress::class, 'address_id');
    }
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
    public function scopePickupType($query)
    {
        return $query->where('delivery_type', 'pickup');
    }
    public function scopeDropOffType($query)
    {
        return $query->where('delivery_type', 'drop_off');
    }
    public function scopeCodPayment($query)
    {
        return $query->where('payment_method', 'cod');
    }
    public function scopeWalletPayment($query)
    {
        return $query->where('payment_method', 'wallet');
    }
    public function isPickupType()
    {
        return $this->delivery_type == DeliveryType::PICKUP;
    }
    public function isDropOffType()
    {
        return $this->delivery_type == DeliveryType::DROP_OFF;
    }
    public function isCodPayment()
    {
        return $this->payment_method == 'cod';
    }
    public function isWalletPayment()
    {
        return $this->payment_method == 'wallet';
    }
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }
    public function statusAlreadyCancelled()
    {
        return $this->status == 'cancelled';
    }
    public function isCompleted()
    {
        return $this->status == 'delivered';
    }
    public function isFailed()
    {
        return in_array($this->status, ['failed', 'cancelled']);
    }
    public function canBeScheduled()
    {
        return $this->isPickupType() && $this->status == 'confirmed';
    }
    public function canBePickedUp()
    {
        return $this->isPickupType() && in_array($this->status, ['pickup_scheduled', 'confirmed']);
    }
    public function getFullRecipientAddressAttribute()
    {
        return "{$this->recipient_address}, {$this->recipient_city}, {$this->recipient_province} {$this->recipient_postal_code}";
    }
    public function getFullPickupAddressAttribute()
    {
        if (!$this->isPickupType() || !$this->pickupAddress) {
            return null;
        }
        $address = $this->pickupAddress;
        return "{$address->address}, {$address->city}, {$address->province} {$address->postal_code}";
    }
    public static function generatePickupCode()
    {
        $prefix = 'PU'; 
        return $prefix . now()->format('ymd') . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }
    public function updateBuyerRating()
    {
        $buyerRating = BuyerRating::findOrCreateByPhone($this->recipient_phone, $this->recipient_name);
        $isSuccessful = $this->status == 'delivered';
        $isCancelled = $this->status == 'cancelled';
        $isFailed = $this->status == 'failed';
        $buyerRating->updateStats($isSuccessful, $isCancelled, $isFailed);
    }
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($pickupRequest) {
            $pickupRequest->pickup_code = static::generatePickupCode();
            $pickupRequest->requested_at = now();
        });
        static::updating(function ($pickupRequest) {
            if ($pickupRequest->isDropOffType()) {
                $pickupRequest->address_id = null;
                $pickupRequest->pickup_scheduled_at = null;
                $pickupRequest->picked_up_at = null;
                if (in_array($pickupRequest->status, ['pickup_scheduled', 'picked_up'])) {
                    $pickupRequest->status = 'in_transit'; 
                }
            }
        });
        static::updated(function ($pickupRequest) {
            if ($pickupRequest->isDirty('status') && in_array($pickupRequest->status, ['delivered', 'failed', 'cancelled'])) {
                $pickupRequest->updateBuyerRating();
            }
        });
    }
}