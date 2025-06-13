<?php

namespace App\Models;

use App\Trait\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class OutletKios extends Model
{
    use HasUuid;

    protected $guarded = [];

    protected $appends = ['statusstring'];

    public function getStatusStringAttribute()
    {
        return $this->status ? '<span class="badge bg-success text-dark">ON</span>' : '<span class="badge bg-secondary">OFF</span>';
    }


    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->timezone(session('zonawaktu'))->isoFormat('DD MMM YYYY HH:mm')
        );
    }
}
