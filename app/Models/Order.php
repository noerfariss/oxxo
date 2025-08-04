<?php

namespace App\Models;

use App\Enums\OrderEnum;
use App\Trait\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUuid;

    protected $guarded = [];

    protected $appends = ['statuslabel'];

    public function getStatusLabelAttribute()
    {
        return OrderEnum::from($this->status)->label();
    }

    protected function kiostext(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $value ? json_encode($value) : null,
            get: fn($value) => $value ? json_decode($value) : null
        );
    }

    protected function membertext(): Attribute
    {
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

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Carbon::parse($value)->timezone(session('zonawaktu'))->isoFormat('DD MMM YYYY HH:mm') : null,
        );
    }
}
