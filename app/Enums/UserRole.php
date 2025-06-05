<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case SELLER = 'seller';

    /**
     * Get all role values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get role labels for display
     */
    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::SELLER => 'Penjual',
        };
    }

    /**
     * Get role descriptions
     */
    public function description(): string
    {
        return match($this) {
            self::ADMIN => 'Memiliki akses penuh ke sistem admin',
            self::SELLER => 'Dapat mengelola produk dan toko',
        };
    }

    /**
     * Check if role has admin privileges
     */
    public function hasAdminPrivileges(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Check if role can manage products
     */
    public function canManageProducts(): bool
    {
        return in_array($this, [self::ADMIN, self::SELLER]);
    }
}