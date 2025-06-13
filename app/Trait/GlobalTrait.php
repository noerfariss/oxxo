<?php

namespace App\Trait;

use App\Models\Calendar;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

trait GlobalTrait
{
    protected function zonawaktu()
    {
        $data = DB::table('settings')->first();
        return $data->timezone;
    }

    protected function user_timezone()
    {
        return request()->user()?->office?->city?->state->timezone ?? config('app.timezone');
    }

    protected function redDays()
    {
        $now = date('Y-m-d');
        $calendars = Calendar::where('dates', $now)->first();
        return $calendars->label;
    }

    protected function RupiahFormat($currency)
    {
        return 'Rp ' . number_format($currency, 0, ',', '.');
    }

    protected function GenerateNumberMember()
    {
        return rand(111111, 999999);
    }

    protected function IntervalDate($dates, $format = 'MM/DD')
    {
        $start = Carbon::parse($dates[0]);
        $end = Carbon::parse($dates[1]);

        $dateArray = [];
        $interval = CarbonPeriod::create($start, $end);
        foreach ($interval as $date) {
            $dateArray[] = $date->isoFormat($format);
        }

        return $dateArray;
    }
}
