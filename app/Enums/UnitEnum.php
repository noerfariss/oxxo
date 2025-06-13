<?php

namespace App\Enums;

enum UnitEnum: int
{
    case PCS = 1;
    case METER = 0;

    public function label(): string
    {
        return match ($this) {
            self::PCS => 'Pcs',
            self::METER => 'Meter',
        };
    }
}
