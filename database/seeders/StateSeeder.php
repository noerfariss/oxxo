<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\State;
use Exception;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv = fopen(base_path('database/seeders/data/provinces.csv'), 'r');
        $getData = fgetcsv($csv, 2000, ",");

        DB::beginTransaction();
        try {
            $firstline = true;
            while (($data = fgetcsv($csv, 2000, ",")) !== FALSE) {
                State::create([
                    'id' => $data[0],
                    'name' => $data[1],
                    'timezone' => $data[2]
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
