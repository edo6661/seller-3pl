<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyerRating extends Model
{
        protected $fillable = [
        'phone_number','name', 'total_orders', 'successful_orders',
        'failed_cod_orders', 'cancelled_orders', 'success_rate',
        'risk_level', 'notes'
    ];

    protected $casts = [
        'success_rate' => 'decimal:2',
    ];

    public function isHighRisk()
    {
        return $this->risk_level === 'high';
    }

    public function isMediumRisk()
    {
        return $this->risk_level === 'medium';
    }

    public function isLowRisk()
    {
        return $this->risk_level === 'low';
    }

    public function getRiskWarningAttribute()
    {
        switch ($this->risk_level) {
            case 'high':
                return 'PERINGATAN: Buyer ini memiliki tingkat kegagalan tinggi (' . (100 - $this->success_rate) . '%)';
            case 'medium':
                return 'PERHATIAN: Buyer ini memiliki riwayat kegagalan sedang (' . (100 - $this->success_rate) . '%)';
            default:
                return null;
        }
    }

    public function scopeHighRisk($query)
    {
        return $query->where('risk_level', 'high');
    }

    public function scopeByPhone($query, $phone)
    {
        return $query->where('phone_number', $phone);
    }

}
