<?php

namespace App\Trait;

use App\Enums\AttendanceStatusEnum;
use App\Models\Attendance;
use App\Models\Charge;

trait AttendanceTrait
{
    public $ChargeData;

    public function checkAvailable($dates)
    {
        $user = request()->user()->id;

        $data = Attendance::query()
            ->where('member_id', $user)
            ->whereIn('dates', $dates)
            ->whereIn('status', [AttendanceStatusEnum::ACTIVE->value, AttendanceStatusEnum::PENDING->value, AttendanceStatusEnum::REJECT->value])
            ->count();

        return $data;
    }

    protected function AttendancePenalty(int $member, int $time_late_minute)
    {
        $data = Charge::query()
            ->whereHas(
                'office',
                fn($e) => $e->whereHas(
                    'members',
                    fn($e) => $e->where('id', $member)
                )
            )
            ->select('office_id', 'start', 'end', 'nominal');

        if ($data->count() > 0) {

            $collect = $data
                ->where('start', '<=', $time_late_minute)
                ->where('end', '>=', $time_late_minute)
                ->first();

            if ($collect !== null) {
                return $collect->nominal;
            } else {
                $data = Charge::query()
                    ->whereHas(
                        'office',
                        fn($e) => $e->whereHas(
                            'members',
                            fn($e) => $e->where('id', $member)
                        )
                    )
                    ->max('nominal');

                return $data;
            }
        } else {
            return 0;
        }
    }
}
