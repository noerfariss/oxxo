<?php

namespace App\Models;

use App\Enums\DepositEnum;
use App\Trait\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $guarded = [];

    protected $appends = ['typestring'];

    public function getTypeStringAttribute()
    {
        return DepositEnum::from((int) $this->type)->label();
    }

    public function amount(): Attribute
    {
        return Attribute::make(
            get: fn($value) => number_format($value, 2),
        );
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Carbon::parse($value)->timezone(session('zonawaktu'))->isoFormat('DD MMM YYYY HH:mm') : null,
        );
    }
}
