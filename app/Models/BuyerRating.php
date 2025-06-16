<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\RiskLevel;

class BuyerRating extends Model
{
    protected $fillable = [
        'phone_number', 'name', 'total_orders', 'successful_orders',
        'failed_cod_orders', 'cancelled_orders', 'success_rate',
        'risk_level', 'notes'
    ];

    protected $casts = [
        'success_rate' => 'decimal:2',
        'risk_level' => RiskLevel::class,
    ];

    public function findOrCreateByPhone(string $phone, string $name): self
    {
        return self::firstOrCreate(
            ['phone_number' => $phone],
            [
                'name' => $name,
                'total_orders' => 0,
                'successful_orders' => 0,
                'failed_cod_orders' => 0,
                'cancelled_orders' => 0,
                'success_rate' => 0.00,
                'risk_level' => RiskLevel::LOW,
                'notes' => null
            ]

        );
    }

    public function isHighRisk(): bool
    {
        return $this->risk_level === RiskLevel::HIGH;
    }

    public function isMediumRisk(): bool
    {
        return $this->risk_level === RiskLevel::MEDIUM;
    }

    public function isLowRisk(): bool
    {
        return $this->risk_level === RiskLevel::LOW;
    }

    public function getRiskWarningAttribute(): ?string
    {
        return match($this->risk_level) {
            RiskLevel::HIGH => 'PERINGATAN: Buyer ini memiliki tingkat kegagalan tinggi (' . (100 - $this->success_rate) . '%)',
            RiskLevel::MEDIUM => 'PERHATIAN: Buyer ini memiliki riwayat kegagalan sedang (' . (100 - $this->success_rate) . '%)',
            RiskLevel::LOW => null,
        };
    }

    public function scopeHighRisk($query)
    {
        return $query->where('risk_level', RiskLevel::HIGH->value);
    }

    public function scopeMediumRisk($query)
    {
        return $query->where('risk_level', RiskLevel::MEDIUM->value);
    }

    public function scopeLowRisk($query)
    {
        return $query->where('risk_level', RiskLevel::LOW->value);
    }

    public function scopeByPhone($query, $phone)
    {
        return $query->where('phone_number', $phone);
    }
}
