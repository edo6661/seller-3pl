<?php

namespace App\Enums;

enum SellerVerificationStatus: string
{
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Verifikasi',
            self::VERIFIED => 'Terverifikasi',
            self::REJECTED => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::VERIFIED => 'green',
            self::REJECTED => 'red',
        };
    }
}