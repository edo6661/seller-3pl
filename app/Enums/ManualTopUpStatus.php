<?php
namespace App\Enums;

enum ManualTopUpStatus: string
{
    case PENDING = 'pending';
    case WAITING_PAYMENT = 'waiting_payment';
    case WAITING_APPROVAL = 'waiting_approval';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu',
            self::WAITING_PAYMENT => 'Menunggu Pembayaran',
            self::WAITING_APPROVAL => 'Menunggu Persetujuan',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::WAITING_PAYMENT => 'info',
            self::WAITING_APPROVAL => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::CANCELLED => 'secondary',
        };
    }
}