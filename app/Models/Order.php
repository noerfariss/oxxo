<?php

namespace App\Models;

use App\Trait\HasUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUuid;

    protected $guarded = [];

    protected function kiostext(): Attribute{
        return Attribute::make(
            set: fn($value) => $value ? json_encode($value) : null,
            get: fn($value) => $value ? json_decode($value) : null
        );
    }

    protected function productId(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $value ? json_encode($value) : null,
            get: fn($value) => $value ? json_decode($value) : null
        );
    }

    protected function products(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $value ? json_encode($value) : null,
            get: fn($value) => $value ? json_decode($value) : null
        );
    }
}
