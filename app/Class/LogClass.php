<?php

namespace App\Class;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
use Ramsey\Uuid\Uuid;

class LogClass
{
    public static function set($description = '', $isMobile = false)
    {
        $data = $description;

        if ($isMobile) {
            DB::beginTransaction();
            try {
                DB::table('log_device')->insert([
                    'uuid' => Uuid::uuid7(),
                    'member_id' => Auth::guard('api')->id(),
                    'description' => $data,
                    'ip' => request()->ip(),
                    'url' => url()->current(),
                    'created_at' => Carbon::now()
                ]);
                DB::commit();
            } catch (\Throwable $th) {
                Log::warning($th->getMessage());
                DB::rollBack();
            }

        } else {
            $agent = new Agent();

            $url = url()->current();
            $device = $agent->device();
            $deviceVersion = $agent->version($device);
            $platform = $agent->platform();
            $platformVersion = $agent->version($platform);
            $browser = $agent->browser();
            $browserVersion = $agent->version($browser);
            $ip = request()->ip();

            DB::beginTransaction();
            try {
                DB::table('logs')->insert([
                    'uuid' => Uuid::uuid7(),
                    'description' => gettype($data) === 'array' ? $data['description'] : $data,
                    'user_id' => Auth::check() ? Auth::id() : null,
                    'ip' => $ip,
                    'url' => $url,
                    'device' => $device,
                    'device_version' => $deviceVersion,
                    'platform' => $platform,
                    'platform_version' => $platformVersion,
                    'browser' => $browser,
                    'browser_version' => $browserVersion,
                    'created_at' => now(),
                ]);
                DB::commit();
            } catch (\Throwable $th) {
                Log::warning($th->getMessage());
                DB::rollBack();
            }
        }
    }
}
