<?php

namespace App\Http\Controllers\Api;

use App\Class\LogClass;
use App\Class\MemberClass;
use App\Class\ResponseClass;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OvertimeCreateRequest;
use App\Http\Requests\Api\OvertimeDoneRequest;
use App\Models\Overtime;
use App\Models\OvertimeLog;
use App\Trait\CheckLogTrait;
use App\Trait\OvertimeTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OvertimeController extends Controller
{
    use CheckLogTrait, OvertimeTrait;

    public function index(Request $request)
    {
        try {
            $user = request()->user()->id;
            $data = Overtime::query()
                ->whereHas('members', fn($e) => $e->where('id', $user))
                ->with('log')
                ->where('status', true)
                ->orderBy('id','desc')
                ->simplePaginate(7);

            LogClass::set('Overtime list', true);

            return ResponseClass::success(data: $data);
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            return ResponseClass::error();
        }
    }

    public function show(Overtime $overtime)
    {
        $overtime['log'] = $overtime->log;

        return ResponseClass::success(data: $overtime);
    }

    public function login(OvertimeCreateRequest $request, Overtime $overtime)
    {
        DB::beginTransaction();
        try {
            $user = request()->user();

            // cek waktu lembur
            $now = Carbon::now();
            $startOvertime = Carbon::parse($overtime->start);
            if ($now->lessThan($startOvertime)) {
                return ResponseClass::error('Waktu lembur belum dimulai', statusCode: 403);
            }

            // detail member
            $member = MemberClass::updatecache();
            $defaultTimes = $member->office;

            // cek jarak posisi checkin dari lokasi
            $position = $this->checkPosition($request->lat_in, $request->lon_in, $defaultTimes->latitude, $defaultTimes->longitude);
            $position = (int) str_replace(',', '', $position);

            if ($position > $defaultTimes->radius) {
                return ResponseClass::error('Jarak terlalu jauh dari posisi checklog', [
                    'radius' => $defaultTimes->radius,
                    'position' => $position
                ], 401);
            }

            // cek sudah pernah diinput?
            $checkDuplicate = $this->checkAvailableOvertime($overtime);

            if ($checkDuplicate) {
                return ResponseClass::error('Anda sudah pernah memulai lembur ini', statusCode: 403);
            }

            // input login lembur
            $overtime = OvertimeLog::create([
                'dates' => $request->dates,
                'overtime_id' => $overtime->id,
                'member_id' => $user->id,
                'members' => $member,
                'time_default_in' => $overtime->start,
                'time_in' => $request->time_in,
                'address_in' => $request->address_in,
                'lat_in' => $request->lat_in,
                'lon_in' => $request->lon_in,
                'accuracy_in' => $request->accuracy_in
            ]);

            LogClass::set('Overtime start ', true);

            DB::commit();
            return ResponseClass::success('Proses lembur dimulai', data: $overtime);
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            DB::rollBack();
            return ResponseClass::error();
        }
    }

    public function logdone(OvertimeDoneRequest $request, Overtime $overtime, OvertimeLog $overtimelog)
    {
        DB::beginTransaction();
        try {
            // detail member
            $member = MemberClass::updatecache();
            $defaultTimes = $member->office;

            // cek jarak posisi checkin dari lokasi
            $position = $this->checkPosition($request->lat_out, $request->lon_out, $defaultTimes->latitude, $defaultTimes->longitude);
            $position = (int) str_replace(',', '', $position);

            if ($position > $defaultTimes->radius) {
                return ResponseClass::error('Jarak terlalu jauh dari posisi checklog', [
                    'radius' => $defaultTimes->radius,
                    'position' => $position
                ], 401);
            }

            // input logout lembur
            $overtimelog->update([
                'time_default_out' => $overtime->end,
                'time_out' => $request->time_out,
                'address_out' => $request->address_out,
                'lat_out' => $request->lat_out,
                'lon_out' => $request->lon_out,
                'accuracy_out' => $request->accuracy_out
            ]);

            LogClass::set('Overtime end ', true);

            DB::commit();
            return ResponseClass::success('Proses lembur selesai');
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            DB::rollBack();
            return ResponseClass::error();
        }
    }
}
