<?php

namespace App\Class;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingClass
{
    public static function updatecache($clear = false)
    {
        if ($clear) {
            Cache::forget('umum');
        }

        $data = Cache::remember('umum', 1440, function () {
            return Setting::first();
        });

        return $data;
    }
}
