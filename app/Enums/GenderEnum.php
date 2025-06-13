<?php

namespace App\Enums;

enum GenderEnum: int
{
    case MALE = 0;
    case FEMALE = 1;

    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Laki-laki',
            self::FEMALE => 'Perempuan',
        };
    }
}
