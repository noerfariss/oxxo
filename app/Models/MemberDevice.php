<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class MemberDevice extends Model
{
    protected $guarded = [];

    public function devices(): Attribute
    {
        return Attribute::make(
            set: fn($value) => json_encode($value),
            get: fn($value) => $value ? json_decode($value) : null,
        );
    }
}
