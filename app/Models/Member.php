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

    protected $appends = ['statusstring', 'genderstring'];

    public function getStatusStringAttribute()
    {
        return $this->status ? '<span class="badge bg-success text-dark">ON</span>' : '<span class="badge bg-secondary">OFF</span>';
    }

    public function getGenderStringAttribute()
    {
        return GenderEnum::from($this->gender)->label();
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function overtime()
    {
        return $this->belongsToMany(Overtime::class, 'member_overtime', 'member_id', 'overtime_id');
    }

    public function devices()
    {
        return $this->hasMany(MemberDevice::class, 'member_id');
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

    protected function end(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->timezone($this->user_timezone())->isoFormat('HH:mm'),
        );
    }

    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Carbon::parse($value)->timezone(session('zonawaktu'))->isoFormat('DD MMM YYYY HH:mm') : null,
        );
    }
}
