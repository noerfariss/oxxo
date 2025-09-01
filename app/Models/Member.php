<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Trait\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Member extends Authenticable implements JWTSubject
{
    use HasFactory, HasUuid;

    protected $guarded = [];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $appends = ['statusstring', 'genderstring', 'namestring', 'memberstring'];

    public function getStatusStringAttribute()
    {
        return $this->status ? '<span class="badge bg-success text-dark">ON</span>' : '<span class="badge bg-secondary">OFF</span>';
    }

    public function getMemberStringAttribute()
    {
        return $this->is_member ? '<span class="badge bg-success text-dark">MEMBER</span>' : '<span class="badge bg-secondary">NON MEMBER</span>';
    }

    public function getGenderStringAttribute()
    {
        return GenderEnum::from($this->gender)->label();
    }

    public function getNameStringAttribute()
    {
        return $this->name . ' ' . GenderEnum::from($this->gender)->label();
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function kios()
    {
        return $this->belongsTo(OutletKios::class, 'kios_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function photo(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? url('/storage') . '/' . $value : null,
        );
    }

    public function name(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => strtoupper($value),
        );
    }

    public function deposit()
    {
        return $this->hasMany(Deposit::class, 'member_id');
    }

    public function latestCutOff()
    {
        return $this->hasOne(DepositCutOff::class, 'member_id')->latest('id');
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Carbon::parse($value)->timezone(session('zonawaktu'))->isoFormat('DD MMM YYYY HH:mm') : null,
        );
    }
}
