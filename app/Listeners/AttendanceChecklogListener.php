<?php

namespace App\Listeners;

use App\Enums\AttendanceEnum;
use App\Enums\CheckLogEnum;
use App\Events\AttendanceChecklogEvent;
use App\Models\Attendance;
use App\Models\CheckLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AttendanceChecklogListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AttendanceChecklogEvent $event): void
    {
        $member_id = $event->member;

        $datas = CheckLog::where([
            'member_id' => $member_id,
            'dates' => date('Y-m-d'),
        ])
            ->whereIn('type', [CheckLogEnum::CHECKIN, CheckLogEnum::CHECKOUT])
            ->select('type', 'dates', 'time', 'time_late_string', 'reason')
            ->get();

        $collect = collect($datas);

        $time_in = $collect->first(function ($item) {
            return $item->type === CheckLogEnum::CHECKIN->value;
        });

        $time_out = $collect->first(function ($item) {
            return $item->type === CheckLogEnum::CHECKOUT->value;
        });

        Attendance::create([
            'member_id' => $member_id,
            'dates' => date('Y-m-d'),
            'type' => AttendanceEnum::WORK,
            'description' => $time_in->time_late_string ? 'Terlambat ' . $time_in->time_late_string : NULL,
            'time_in' => $time_in->time,
            'time_out' => $time_out->time,
        ]);
    }
}
