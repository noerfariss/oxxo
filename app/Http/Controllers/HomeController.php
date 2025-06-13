<?php

namespace App\Http\Controllers;

use App\Class\ResponseClass;
use App\Enums\AttendanceEnum;
use App\Enums\AttendanceStatusEnum;
use App\Models\Attendance;
use App\Models\Calendar;
use App\Models\CheckLog;
use App\Models\Division;
use App\Models\Member;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        return view('member.index');
    }

    public function memberCount()
    {
        try {
            $data = Member::where('status', true)->count();
            return ResponseClass::success(data: $data);
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            return ResponseClass::error();
        }
    }

    public function officeCount()
    {
        try {
            $data = Office::where('status', true)->count();
            return ResponseClass::success(data: $data);
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            return ResponseClass::error();
        }
    }

    public function divisionCount()
    {
        try {
            $data = Division::where('status', true)->count();
            return ResponseClass::success(data: $data);
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            return ResponseClass::error();
        }
    }

    public function dailyAttendance(Request $request)
    {
        $dates = pecahTanggal($request->dates);

        try {
            $calendar = Calendar::query()
                ->whereBetween('dates', [$dates[0], $dates[1]])
                ->pluck('dates');

            $checklog = CheckLog::query()
                ->whereBetween('dates', [$dates[0], $dates[1]])
                ->select('dates', 'member_id')
                ->get();

            $attendances = Attendance::query()
                ->whereBetween('dates', [$dates[0], $dates[1]])
                ->where('status', AttendanceStatusEnum::ACTIVE->value)
                ->select('dates', 'member_id', 'type')
                ->get();

            $groupDates = collect($calendar)->map(function ($date) use ($checklog, $attendances) {
                return [
                    'date' => $date,
                    'work' => collect($checklog)->where('dates', $date)->groupBy('member_id')->count(),
                    'sick' => collect($attendances)->where('dates', $date)->where('type', AttendanceEnum::SICK->value)->groupBy('member_id')->count(),
                    'permit' => collect($attendances)->where('dates', $date)->where('type', AttendanceEnum::PERMIT->value)->groupBy('member_id')->count(),
                    'cuti' => collect($attendances)->where('dates', $date)->where('type', AttendanceEnum::CUTI->value)->groupBy('member_id')->count(),
                ];
            });

            return ResponseClass::success(data: $groupDates);
        } catch (\Throwable $th) {
            Log::warning($th->getMessage());
            return ResponseClass::error();
        }
    }
}
