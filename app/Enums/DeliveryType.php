<?php

namespace App\Enums;

enum DeliveryType: string
{
    case PICKUP = 'pickup';
    case DROP_OFF = 'drop_off';
    public function label(): string
    {
        return match ($this) {
            self::PICKUP => 'Pickup',
            self::DROP_OFF => 'Drop Off',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PICKUP => 'blue',
            self::DROP_OFF => 'orange',
        };
    }
}