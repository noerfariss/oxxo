<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Exception;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv = fopen(base_path('database/seeders/data/kota.csv'), 'r');
        $getData = fgetcsv($csv, 2000, ",");

        DB::beginTransaction();
        try {
            $firstline = true;
            while (($data = fgetcsv($csv, 2000, ",")) !== FALSE) {
                City::create([
                    'id'        => $data[0],
                    'state_id' => $data[1],
                    'name'      => $data[2],
                ]);
                $firstline = false;
            }
            DB::commit();
            fclose($csv);
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
