<?php

namespace App\Enums;

enum WalletTransactionStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';

    /**
     * Get all status values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get status labels for display
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu',
            self::SUCCESS => 'Berhasil',
            self::FAILED => 'Gagal',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    /**
     * Get status descriptions
     */
    public function description(): string
    {
        return match($this) {
            self::PENDING => 'Transaksi sedang diproses',
            self::SUCCESS => 'Transaksi berhasil diselesaikan',
            self::FAILED => 'Transaksi gagal diproses',
            self::CANCELLED => 'Transaksi dibatalkan',
        };
    }

    /**
     * Get status color for display
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'pending',
            self::SUCCESS => 'success',
            self::FAILED => 'danger',
            self::CANCELLED => 'warning',
        };
    }

    /**
     * Check if status is final (completed)
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::SUCCESS, self::FAILED, self::CANCELLED]);
    }
}