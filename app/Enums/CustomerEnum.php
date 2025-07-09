<?php

namespace App\Enums;

enum CustomerEnum: int
{
    case NONMEMBER = 0;
    case MEMBER = 1;

    public function label(): string
    {
        return match ($this) {
            self::NONMEMBER => 'Member',
            self::MEMBER => '-',
        };
    }
}
