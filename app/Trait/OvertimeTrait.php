<?php

namespace App\Trait;

use App\Models\Overtime;
use Illuminate\Support\Facades\DB;

trait OvertimeTrait
{
    public function OvertimeStatus($start, $end)
    {
        if ($start && $end) {
            return 'Selesai';
        } else if (!$start && !$end) {
            return 'Baru';
        } else {
            return 'Proses';
        }
    }

    public function checkAvailableOvertime(Overtime $overtime)
    {
        $user = request()->user()->id;

        $data = DB::table('overtime_logs as a')
            ->where('member_id', $user)
            ->where('overtime_id', $overtime->id)
            ->where('dates', date('Y-m-d'))
            ->first();

        if ($data == null) {
            return false;
        }

        return true;
    }
}
