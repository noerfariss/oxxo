<?php

namespace App\Http\Controllers;

use App\Class\LogClass;
use App\Class\SettingClass;
use App\Http\Requests\Setting\SettingUpdateRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:PENGATURAN_READ', only: ['index']),
            new Middleware('permission:PENGATURAN_EDIT', only: ['edit', 'update']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $setting = SettingClass::updatecache();
        $title_page = 'Setting';

        return view('member.setting.edit', compact('setting','title_page'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setting $setting)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SettingUpdateRequest $request, Setting $setting)
    {
        DB::beginTransaction();
        try {
            Setting::query()->update($request->only(['name',  'address', 'logo', 'email', 'phone', 'timezone', 'city_id']));

            // Log
            LogClass::set('Update setting');

            DB::commit();

            // reset cache umum
            SettingClass::updatecache(true);

            session(['zonawaktu' => $request->timezone]);

            return redirect()->back()->with('pesan', '<div class="alert alert-success">Pengaturan berhasil diperbarui</div>');
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            DB::rollBack();

            return redirect()->back()->with('pesan', '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>');
        }
    }

    public function updateImage(Request $request, Setting $setting)
    {

        DB::beginTransaction();
        try {
            if ($request->has('logo')) {
                $setting->update($request->only(['logo']));
                // Log
                LogClass::set('Update logo');
            } else {
                $setting->update($request->only(['favicon']));
                // Log
                LogClass::set('Update favicon');
            }

            DB::commit();

            // reset cache umum
            SettingClass::updatecache(true);

            session(['zonawaktu' => $request->timezone]);

            return redirect()->back()->with('pesan', '<div class="alert alert-success">Pengaturan berhasil diperbarui</div>');
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            DB::rollBack();

            return redirect()->back()->with('pesan', '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
