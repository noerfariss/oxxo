<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Payment extends Model
{
    protected $guarded = [];

    protected function instructions(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value)
        );
    }

    protected function paymentDate(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value
                ? Carbon::parse($value)->timezone('Asia/Jakarta')->isoFormat('DD MMM YYYY HH:mm')
                : ''
        );
    }

    protected function expiredTime(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::createFromTimestamp($value)->timezone('Asia/Jakarta')->isoFormat('DD MMM YYYY HH:mm')
        );
    }
}
