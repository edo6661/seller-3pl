<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawRequest extends Model
{

    protected $fillable = [
        'user_id',
        'withdrawal_code',
        'amount',
        'admin_fee',
        'bank_name',
        'account_number',
        'account_name',
        'status',
        'requested_at',
        'processed_at',
        'completed_at',
        'admin_notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeMorningBatch($query)
    {
        return $query->where('batch_time', 'morning');
    }

    public function scopeAfternoonBatch($query)
    {
        return $query->where('batch_time', 'afternoon');
    }

    // Helpers
    public static function generateWithdrawalCode()
    {
        return 'WD' . now()->format('ymd') . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }

    public static function determineBatchTime()
    {
        $hour = now()->hour;
        return $hour < 17 ? 'afternoon' : 'morning'; // WD sebelum jam 5 sore masuk batch sore
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($withdrawal) {
            $withdrawal->withdrawal_code = static::generateWithdrawalCode();
            $withdrawal->requested_at = now();
            $withdrawal->batch_time = static::determineBatchTime();
        });
    }

}
