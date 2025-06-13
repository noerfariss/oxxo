<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = User::create([
            'name' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('0ng15n4D3'),
        ]);

        $superadmin->syncRoles('superadmin');

        $owner = User::create([
            'name' => 'owner',
            'email' => 'owner@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $owner->syncRoles('superadmin');
    }
}
