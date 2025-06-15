<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\OutletKios;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OutletKiosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // all products
        $products = Product::all();


        $kios1 = OutletKios::create([
            'office_id' => Office::first()->id,
            'name' => 'Oxxo Kios Pakuwon Mall Surabaya',
            'address' => 'Jl. Mayjend. Jonosewojo No.2, Babatan, Kec. Wiyung, Surabaya, Jawa Timur 60227',
            'city_id' => 3578,
            'latitude' => '-7.2891006580566575',
            'longitude' => '112.67575085963747',
        ]);



        OutletKios::create([
            'name' => 'Oxxo Kios Grand City Mall Surabaya',
            'address' => 'Jl. Walikota Mustajab No.1, Ketabang, Kec. Genteng, Surabaya, Jawa Timur 60272',
            'city_id' => 3578,
            'latitude' => '-7.262011063834107',
            'longitude' => '112.75017678565611',
        ]);

        OutletKios::create([
            'name' => 'Oxxo Kios Lippo Plaza Sidoarjo',
            'address' => 'Jl. Jati Raya No.1, Jati, Kec. Sidoarjo, Kabupaten Sidoarjo, Jawa Timur 61226',
            'city_id' => 3515,
            'latitude' => '-7.446130476179654',
            'longitude' => '112.69844922613463',
        ]);
    }
}
