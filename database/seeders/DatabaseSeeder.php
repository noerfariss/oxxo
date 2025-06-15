<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
        ]);

        $this->call([
            StateSeeder::class,
            CitySeeder::class,
            DistrictSeeder::class,
            SettingSeeder::class,
            // CalendarSeeder::class,
            CategorySeeder::class,
            ProductAttributeSeeder::class,
            ProductSeeder::class,
            OfficeSeeder::class,
            OutletKiosSeeder::class,
        ]);
    }
}
