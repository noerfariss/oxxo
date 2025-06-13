<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Trait\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasUuid;


    protected $guarded = [];

    protected $appends = ['statusstring', 'rolestring', 'photourl', 'timeinput'];

    public function getStatusStringAttribute()
    {
        return $this->status ? '<span class="badge bg-success text-dark">ON</span>' : '<span class="badge bg-secondary">OFF</span>';
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo ?: url('/images/anggota/pria.jpg');
    }

    public function photo(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? url('/storage') . '/' . $value : null,
        );
    }

    public function getRoleStringAttribute()
    {
        return $this->roles->first()->name;
    }

    public function getTimeInputAttribute()
    {
        return Carbon::parse($this->created_at)->isoFormat('DD MMM YYYY HH:mm');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
