<?php

namespace App\Http\Controllers\Api;

use App\Class\LogClass;
use App\Class\MemberClass;
use App\Class\ResponseClass;
use App\Enums\AttendanceEnum;
use App\Enums\AttendanceStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AttendanceCreateRequest;
use App\Models\Attendance;
use App\Trait\ApiPhoto;
use App\Trait\AttendanceTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    use ApiPhoto, AttendanceTrait;

    public function index(Request $request)
    {
        try {
            $member_id = request()->user()->id;
            $data = Attendance::where('member_id', $member_id)
                ->where('status', '<>', AttendanceStatusEnum::DELETE)
                ->orderBy('id', 'desc')
                ->simplePaginate(10);

            LogClass::set('Attendance list', true);

            return ResponseClass::success(data: $data);
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            return ResponseClass::error();
        }
    }

    public function store(AttendanceCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $dateExp = explode(',', $request->dates);
            $checkAvailable = $this->checkAvailable($dateExp);
            if ($checkAvailable) {
                return ResponseClass::error('Tanggal absensi sudah pernah diinputkan', statusCode: 403);
            }

            $members = MemberClass::updatecache();
            $photo = $request->type != AttendanceEnum::SICK->value ? null : ($request->has('photo') ? $this->savePhoto($request->photo, 'attendance') : null);

            foreach ($dateExp as $date) {
                Attendance::create([
                    'dates' => $date,
                    'member_id' => $request->member_id,
                    'members' => $members,
                    'type' => $request->type,
                    'description' => $request->description,
                    'photo' => $photo,
                    'status' => AttendanceStatusEnum::PENDING
                ]);
            }

            LogClass::set('Input Attendance ' . AttendanceEnum::from((int) $request->type)->label(), true);

            DB::commit();

            LogClass::set('Input absensi ', true);

            return ResponseClass::success('Absensi berhasil diinputkan');
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            DB::rollBack();
            return ResponseClass::error();
        }
    }

    public function show(Attendance $attendance)
    {
        return ResponseClass::success(data: $attendance);
    }
}
