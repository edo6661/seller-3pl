<?php

namespace App\Enums;
enum RiskLevel: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    /**
     * Get all risk level values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get risk level labels for display
     */
    public function label(): string
    {
        return match($this) {
            self::LOW => 'Rendah',
            self::MEDIUM => 'Sedang',
            self::HIGH => 'Tinggi',
        };
    }

    /**
     * Get risk level descriptions
     */
    public function description(): string
    {
        return match($this) {
            self::LOW => 'Buyer dengan tingkat keberhasilan tinggi (≥80%)',
            self::MEDIUM => 'Buyer dengan tingkat keberhasilan sedang (60-79%)',
            self::HIGH => 'Buyer dengan tingkat keberhasilan rendah (<60%)',
        };
    }

    /**
     * Get risk level color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::LOW => 'green',
            self::MEDIUM => 'yellow',
            self::HIGH => 'red',
        };
    }

    /**
     * Check if risk level is high
     */
    public function isHigh(): bool
    {
        return $this === self::HIGH;
    }

    /**
     * Check if risk level requires warning
     */
    public function requiresWarning(): bool
    {
        return in_array($this, [self::MEDIUM, self::HIGH]);
    }
}
