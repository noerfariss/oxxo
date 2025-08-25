<?php

namespace App\Providers;

use App\Class\SettingClass;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        view()->composer('*', function ($e) {
            $umum = SettingClass::updatecache();
            $timezone = $umum->timezone;

            if (!session()->has('zonawaktu')) {
                session(['zonawaktu' => $timezone]);
            }

            $e->with([
                'tanggal_sekarang' => Carbon::now()->timezone($timezone)->isoFormat('dddd, DD MMMM YYYY'),
                'zonawaktu' => $timezone,
                'title_web' => $umum->name,
                'logo' => ($umum->logo === NULL || $umum->logo === '' || $umum->logo == 'logo') ? $umum->name : '<img src="' . $umum->logo . '" height="10" class="img-fluid">',
                'favicon' => ($umum->favicon === NULL || $umum->favicon === '') ? url('/images/favicon.png') : $umum->favicon,
            ]);
        });
    }
}
