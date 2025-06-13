<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['faviconurl', 'logourl'];

    public function getFaviconUrlAttribute()
    {
        return $this->favicon ?: url('/images/anggota/pria.jpg');
    }

    public function favicon(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? url('/storage') . '/' . $value : null,
        );
    }

    public function getLogoUrlAttribute()
    {
        return $this->photo ?: url('/images/anggota/pria.jpg');
    }

    public function logo(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? url('/storage') . '/' . $value : null,
        );
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
