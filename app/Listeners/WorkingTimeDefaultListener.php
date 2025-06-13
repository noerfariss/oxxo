<?php

namespace App\Listeners;

use App\Events\WorkingTimeDefaultEvent;
use App\Models\WorkingTime;
use App\Models\WorkingTimeDetail;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class WorkingTimeDefaultListener
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
    public function handle(WorkingTimeDefaultEvent $event): void
    {
        $workingtime = $event->workingtime;
        $days = request()->days;
        $checkin = request()->checkin;
        $checkout = request()->checkout;

        // Update default jam kerja yang digunakan
        if ($workingtime->isdefault == 1) {
            WorkingTime::query()
                ->where('office_id', $workingtime->office_id)
                ->where('id', '<>', $workingtime->id)
                ->update(['isdefault' => false]);
        }

        // Update working time detail
        WorkingTimeDetail::where('working_time_id', $workingtime->id)->delete();

        if (count($days) > 0) {
            foreach ($days as $key => $item) {
                WorkingTimeDetail::create([
                    'working_time_id' => $workingtime->id,
                    'day' => json_encode($item),
                    'checkin' => Carbon::parse($checkin[$key]),
                    'checkout' => Carbon::parse($checkout[$key])
                ]);
            }
        }
    }
}
