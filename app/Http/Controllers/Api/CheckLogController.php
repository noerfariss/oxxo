<?php

namespace App\Http\Controllers\Api;

use App\Class\LogClass;
use App\Class\MemberClass;
use App\Class\ResponseClass;
use App\Enums\CheckLogEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ChecklogCreateRequest;
use App\Http\Requests\Api\ChecklogUpdateRequest;
use App\Models\CheckLog;
use App\Trait\ApiPhoto;
use App\Trait\CheckLogTrait;
use App\Trait\GlobalTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CheckLogController extends Controller
{
    use ApiPhoto, CheckLogTrait, GlobalTrait;

    public function in(ChecklogCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $defaultTimes = $this->getDefaultChecklog();

            if (!$defaultTimes) {
                return ResponseClass::error('Tidak ada jadwal kerja', statusCode: 404);
            }

            // cek jarak posisi checkin dari lokasi
            $position = $this->checkPosition($request->lat_in, $request->lon_in, $defaultTimes->latitude, $defaultTimes->longitude);
            $position = (int) str_replace(',', '', $position);

            if ($position > $defaultTimes->radius) {
                return ResponseClass::error('Jarak terlalu jauh dari posisi checklog', [
                    'radius' => $defaultTimes->radius,
                    'position' => $position
                ], 401);
            }

            $member = MemberClass::updatecache();

            // cek sudah pernah diinput?
            $checkDuplicate = $this->checkAvailableChecklog();

            if ($checkDuplicate) {
                return ResponseClass::error('Checklog masuk sudah pernah diinputkan');
            }

            // cek keterlambatan
            $checkLate = $this->checkLate($request->time_in, $defaultTimes);

            $data = CheckLog::create([
                'dates' => $request->dates,
                'member_id' => $request->member_id,
                'members' => $member,
                'time_default_in' => $defaultTimes->checkin,
                'time_in' => $request->time_in,
                'address_in' => $request->address_in,
                'lat_in' => $request->lat_in,
                'lon_in' => $request->lon_in,
                'accuracy_in' => $request->accuracy_in,
                'photo_in' => $this->savePhoto($request->photo_in, 'checkin'),
                'time_late_string' => $checkLate[0],
                'time_late_minute' => $checkLate[1],
                'tolerance' => $defaultTimes->tolerance,
                'reason_late' => $checkLate[1] > 0 ? $request->reason_late : null,
                'reason_validation' => $checkLate[1] > 0 ? CheckLogEnum::PENDING->value : CheckLogEnum::APPROVED->value,
            ]);

            LogClass::set('Checklog in', true);

            DB::commit();

            return ResponseClass::success('Checklog masuk berhasil', data: $data);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::warning($th->getMessage());
            return ResponseClass::error();
        }
    }

    public function checkWhenExists()
    {
        $user = request()->user();

        $data = CheckLog::query()
            ->whereNull('time_out')
            ->where('member_id', $user->id)
            ->orderBy('id', 'desc')->first();
        if ($data) {
            return ResponseClass::success('Anda belum checkout dari absensi sebelumnya', data: [
                'status' => false,
                'checkin_uuid' => $data->uuid,
            ]);
        } else {
            return ResponseClass::success('Data checkin kosong', data: [
                'status' => true
            ]);
        }
    }

    public function out(ChecklogUpdateRequest $request, CheckLog $checklog)
    {
        DB::beginTransaction();
        try {
            $defaultTimes = $this->getDefaultChecklog();

            if (!$defaultTimes) {
                return ResponseClass::error('Tidak ada jadwal kerja', statusCode: 404);
            }

            // cek jarak posisi checkout dari lokasi
            $position = $this->checkPosition($request->lat_out, $request->lon_out, $defaultTimes->latitude, $defaultTimes->longitude);
            $position = (int) str_replace(',', '', $position);

            if ($position > $defaultTimes->radius) {
                return ResponseClass::error('Jarak terlalu jauh dari posisi checklog', [
                    'radius' => $defaultTimes->radius,
                    'position' => $position
                ], 401);
            }

            $checklog->update([
                'time_default_out' => $defaultTimes->checkout,
                'time_out' => $request->time_out,
                'address_out' => $request->address_out,
                'lat_out' => $request->lat_out,
                'lon_out' => $request->lon_out,
                'accuracy_out' => $request->accuracy_out,
                'photo_out' => $this->savePhoto($request->photo_out, 'checkout'),
            ]);

            LogClass::set('Checklog out', true);

            DB::commit();

            return ResponseClass::success('Checklog pulang berhasil');
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            DB::rollBack();
            return ResponseClass::error();
        }
    }

    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => ['required'],
            'month' => ['required']
        ]);

        if ($validator->fails()) {
            return ResponseClass::error('Pastikan filter Tahun & Bulan sudah dipilih');
        }

        $year = $request->year;
        $month = $request->month;

        $dates = Carbon::parse($year . '-' . $month);
        $start = $dates->copy()->firstOfMonth()->isoFormat('YYYY-MM-DD');
        $end = $dates->copy()->lastOfMonth()->isoFormat('YYYY-MM-DD');

        try {
            $user = request()->user()->id;
            $data = CheckLog::query()
                ->where('member_id', $user)
                ->whereBetween('dates', [$start, $end])
                ->orderBy('id', 'desc')
                ->simplePaginate(10);

            return ResponseClass::success(data: $data);
        } catch (\Throwable $th) {
            info($th->getMessage());
            return ResponseClass::error();
        }
    }

    public function show(CheckLog $checklog)
    {
        return ResponseClass::success(data: $checklog);
    }
}
