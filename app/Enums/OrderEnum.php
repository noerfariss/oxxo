<?php

namespace App\Enums;

enum OrderEnum: int
{
    case NEW = 1;
    case DONE = 2;
    case OUT = 3;
    case CANCEL = 0;

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'New',
            self::DONE => 'Done',
            self::OUT => 'Out',
            self::CANCEL => 'Cancel'
        };
    }
}
