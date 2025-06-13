<?php

namespace App\Class;

use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MemberClass
{
    public static function updatecache($clear = false)
    {
        if ($clear) {
            Cache::forget('member-' . Auth::guard('api')->id());
        }

        $data = Cache::remember('member-' . Auth::guard('api')->id(), 60, function () {
            return Member::query()
                ->with([
                    'office:id,name,address,latitude,longitude,radius,wfh,city_id',
                    'office.city:id,name,state_id',
                    'office.city.state:id,name,timezone',
                    'office.workingtime',
                    'office.workingtime.working_detail',
                    'division:id,name',
                    'position:id,name'
                ])
                ->select(
                    'id',
                    'uuid',
                    'nik',
                    'name',
                    'email',
                    'phone',
                    'gender',
                    'photo',
                    'status',
                    'office_id',
                    'division_id',
                    'position_id'
                )
                ->find(Auth::guard('api')->id());
        });

        return $data;
    }
}
