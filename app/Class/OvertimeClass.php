<?php

namespace App\Class;

use App\Models\Overtime;
use Illuminate\Support\Facades\DB;

class OvertimeClass
{
    public static function setmembers(array $members, Overtime $overtime, $reset = false)
    {
        if ($reset) {
            DB::table('member_overtime')->where('overtime_id', $overtime->id)->delete();
        }

        foreach ($members as $member) {
            DB::table('member_overtime')->insert([
                'overtime_id' => $overtime->id,
                'member_id' => $member
            ]);
        }
    }
}
