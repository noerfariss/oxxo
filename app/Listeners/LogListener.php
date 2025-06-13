<?php

namespace App\Listeners;

use App\Events\LogEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Str;

class LogListener
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
    public function handle(LogEvent $event): void
    {
        $data = $event->data;

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
                'uuid' => Str::uuid(),
                'description' => gettype($data) === 'array' ? $data['description'] : $data,
                'user_id' => auth()->check() ? auth()->id() : null,
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
