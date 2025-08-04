<?php

namespace App\Enums;

enum OrderEnum: int
{
    case NEW = 1;
    case OUT = 2;
    case IN = 3;
    case CANCEL = 0;

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'New',
            self::OUT => 'Out',
            self::IN => 'In',
            self::CANCEL => 'Cancel'
        };
    }
}
