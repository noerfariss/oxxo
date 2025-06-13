<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Exception;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv = fopen(base_path('database/seeders/data/kecamatan.csv'), 'r');
        $getData = fgetcsv($csv, 2000, ",");

        DB::beginTransaction();
        try {
            $firstline = true;
            while (($data = fgetcsv($csv, 2000, ",")) !== FALSE) {
                District::create([
                    'id'        => $data[0],
                    'city_id'   => $data[1],
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
