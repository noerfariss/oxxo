<?php

namespace App\Trait;

use App\Enums\CheckLogEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait CheckLogTrait
{
    public function getDefaultChecklog()
    {
        $nowDay = strtolower(Carbon::now()->isoFormat('dddd'));
        $user = request()->user()->id;

        $times = DB::table('members as a')
            ->join('offices as b', 'b.id', '=', 'a.office_id')
            ->join('working_times as c', 'c.office_id', '=', 'b.id')
            ->join('working_time_details as d', 'd.working_time_id', '=', 'c.id')
            ->where('a.id', $user)
            ->where('c.isdefault', true)
            ->where('d.day', $nowDay)
            ->select('a.uuid', 'b.name', 'b.latitude', 'b.longitude', 'b.radius', 'b.wfh', 'c.tolerance', 'd.*')
            ->first();

        if ($times == null) {
            return false;
        }

        return $times;
    }

    protected function checkLate($request, $defaultTimes)
    {
        $timeci = Carbon::parse($request)->timezone($this->zonawaktu());
        $defaultci = Carbon::parse($defaultTimes->checkin)->timezone($this->zonawaktu())->addMinutes($defaultTimes->tolerance);

        if ($timeci->greaterThan($defaultci)) {
            $late_minute = $timeci->diff($defaultci);
            if ($late_minute->h > 0) {
                $late_string = $late_minute->h . ' Jam ' . $late_minute->i . ' Menit';
            } else {
                $late_string = $late_minute->i . ' Menit';
            }

            $late_minute_integer = abs($timeci->diffInMinutes($defaultci));
        } else {
            $late_string = null;
            $late_minute_integer = 0;
        }

        return [$late_string, $late_minute_integer];
    }

    protected function checkPosition($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371; // Radius bumi dalam kilometer
        $dLat = $this->deg2rad($lat2 - $lat1);
        $dLon = $this->deg2rad($lon2 - $lon1);
        $a =
            sin($dLat / 2) * sin($dLat / 2) +
            cos($this->deg2rad($lat1)) * cos($this->deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $R * $c * 1000; // Jarak dalam meter

        return number_format($distance);
    }

    private function deg2rad($deg)
    {
        return $deg * (M_PI / 180);
    }

    public function checkAvailableChecklog()
    {
        $user = request()->user()->id;

        $data = DB::table('check_logs as a')
            ->where('member_id', $user)
            ->where('dates', date('Y-m-d'))
            ->where('reason_validation', CheckLogEnum::APPROVED->value)
            ->first();

        if ($data == null) {
            return false;
        }

        return true;
    }

    public function checkLogType(int $type)
    {
        switch ($type) {
            case 1:
                return 'Masuk';
                break;

            case 2:
                return 'Pulang';
                break;

            case 3:
                return 'Mulai Istirahat';
                break;

            case 4:
                return 'Akhiri Istirahat';
                break;

            case 5:
                return 'Mulai Lembur';
                break;

            case 6:
                return 'Lembur Berakhir';
                break;

            default:
                # code...
                break;
        }
    }
}
