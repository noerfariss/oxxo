<?php

namespace App\Enums;

enum DepositEnum: int
{
    case OUT = 0;
    case IN = 1;

    public function label(): string
    {
        return match ($this) {
            self::OUT => 'KELUAR',
            self::IN => 'MASUK',
        };
    }
}
