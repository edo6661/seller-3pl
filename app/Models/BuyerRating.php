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

    public static function findOrCreateByPhone(string $phone, string $name): self
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
    public function updateStats(bool $isSuccessful, bool $isCancelled, bool $isFailed): void
    {
        $this->total_orders++;

        if ($isSuccessful) {
            $this->successful_orders++;
        } elseif ($isCancelled) {
            $this->cancelled_orders++;
        } elseif ($isFailed) {
            $this->failed_cod_orders++;
        }
        // todo: berkemungkinan saalah logika nya, test lagi nanti
        $this->success_rate = ($this->total_orders > 0)
            ? ($this->successful_orders / $this->total_orders) * 100
            : 0;

        if ($this->success_rate < 70 || $this->failed_cod_orders >= 5) {
            $this->risk_level = RiskLevel::HIGH;
        } elseif ($this->success_rate < 90 || $this->failed_cod_orders >= 2) {
            $this->risk_level = RiskLevel::MEDIUM;
        } else {
            $this->risk_level = RiskLevel::LOW;
        }

        $this->save();
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
