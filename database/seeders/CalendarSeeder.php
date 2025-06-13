<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // add Calendar to table calendars
        $start = Carbon::parse('2025-06-01');
        $end = Carbon::parse('2030-12-31');

        $dates = [];
        $periode = CarbonPeriod::create($start, $end);
        foreach ($periode as $date) {
            $dates[] = [
                'dates' =>  $date->isoFormat('YYYY-MM-DD'),
                'week' => $date->week(),
                'days' => $date->isoFormat('dddd'),
                'label' => $date->isoFormat('dddd') === 'Minggu' ? 'red' : 'black',
            ];
        }

        DB::table('calendars')->truncate();
        DB::table('calendars')->insert($dates);

        // === Get All Holidays
        $year_start = 2024;
        $year_end = 2030;
        $holidays = [];

        for ($i = $year_start; $i <= $year_end; $i++) {
            $libur = Http::get('https://kalenderindonesia.com/api/c433ccd7196f3b1e/libur/masehi/' . $i)->object();
            $data = $libur->data->holidays;
            foreach ($data as $item) {
                $holidays[] = [
                    'dates' => $item->masehi,
                    'description' => str_replace('_', ' ', ucwords($item->holiday->name)),
                ];
            }
        }

        // Update holiday in calendars table
        foreach ($holidays as $holiday) {
            DB::table('calendars')
                ->where('dates', $holiday['dates'])
                ->update([
                    'label' => 'red',
                    'description' => $holiday['description'],
                ]);
        }
    }
}
