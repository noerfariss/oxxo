<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Office::create([
            'name' => env('APP_NAME'),
            'address' => 'Jl. Kertajaya No.159, Airlangga, Kec. Gubeng, Surabaya, Jawa Timur 60286',
            'latitude' => '-7.278708',
            'longitude' => '112.760073',
            'city_id' => 3578,
            'radius' => 20,
        ]);
    }
}
