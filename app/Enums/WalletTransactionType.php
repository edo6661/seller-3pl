<?php

namespace App\Enums;

enum WalletTransactionType: string
{
    case TOPUP = 'topup';
    case WITHDRAW = 'withdraw';
    case PAYMENT = 'payment';
    case REFUND = 'refund';

    /**
     * Get all transaction type values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get transaction type labels for display
     */
    public function label(): string
    {
        return match($this) {
            self::TOPUP => 'Top Up',
            self::WITHDRAW => 'Penarikan',
            self::PAYMENT => 'Pembayaran',
            self::REFUND => 'Pengembalian',
        };
    }

    /**
     * Get transaction type descriptions
     */
    public function description(): string
    {
        return match($this) {
            self::TOPUP => 'Menambah saldo ke dompet',
            self::WITHDRAW => 'Menarik saldo dari dompet',
            self::PAYMENT => 'Pembayaran untuk pembelian',
            self::REFUND => 'Pengembalian dana',
        };
    }

    /**
     * Check if transaction type increases balance
     */
    public function increasesBalance(): bool
    {
        return in_array($this, [self::TOPUP, self::REFUND]);
    }

    /**
     * Check if transaction type decreases balance
     */
    public function decreasesBalance(): bool
    {
        return in_array($this, [self::WITHDRAW, self::PAYMENT]);
    }
}