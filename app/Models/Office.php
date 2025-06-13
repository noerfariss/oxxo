<?php

namespace App\Models;

use App\Trait\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Office extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    protected $appends = ['statusstring', 'branchstring'];

    public function getStatusStringAttribute()
    {
        return $this->status ? '<span class="badge bg-success text-dark">ON</span>' : '<span class="badge bg-secondary">OFF</span>';
    }

    public function getBranchStringAttribute()
    {
        return $this->is_branch ? '<span class="badge bg-danger">Cabang</span>' : '';
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'office_id');
    }

    public function kios()
    {
        return $this->hasMany(OutletKios::class);
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->timezone(session('zonawaktu'))->isoFormat('DD MMM YYYY HH:mm')
        );
    }
}
