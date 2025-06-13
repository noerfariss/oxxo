<?php

namespace App\Http\Controllers;

use App\Class\ResponseClass;
use App\Enums\AttendanceEnum;
use App\Enums\AttendanceStatusEnum;
use App\Http\Resources\SuccessResource;
use App\Models\Attendance;
use App\Models\Calendar;
use App\Trait\AttendanceTrait;
use App\Trait\GlobalTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller implements HasMiddleware
{
    use GlobalTrait, AttendanceTrait;

    public static function middleware()
    {
        return [
            new Middleware('permission:ATTENDANCE_CREATE', only: ['create', 'store']),
            new Middleware('permission:ATTENDANCE_READ', only: ['index']),
            new Middleware('permission:ATTENDANCE_EDIT', only: ['edit', 'update']),
            new Middleware('permission:ATTENDANCE_DELETE', only: ['delete']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('member.attendance.index');
    }

    public function ajax(Request $request)
    {
        $dates = pecahTanggal($request->dates);
        $search = $request->search;

        $data = Attendance::query()
            ->when($search, function ($e, $search) {
                $e->where(function ($e) use ($search) {
                    $e->where('members->name', 'like', '%' . $search . '%')
                        ->orWhere('members->email', 'like', '%' . $search . '%')
                        ->orWhere('members->phone', 'like', '%' . $search . '%');
                });
            })
            ->whereBetween('dates', [$dates[0], $dates[1]]);

        return DataTables::eloquent($data)
            ->addColumn('typestring', function ($e) {
                if ($e->type == AttendanceEnum::SICK->value) {
                    return '<span class="me-4 d-flex align-items-center gap-1">
                                <div class="py-2 px-2 badge rounded-circle bg-danger rounded"> </div>
                                Sakit
                            </span>';
                } else if ($e->type == AttendanceEnum::CUTI->value) {
                    return '<span class="me-4 d-flex align-items-center gap-1">
                                <div class="py-2 px-2 badge rounded-circle bg-primary text-dark rounded"> </div>
                                Cuti
                            </span>';
                } else if ($e->type == AttendanceEnum::PERMIT->value) {
                    return '<span class="me-4 d-flex align-items-center gap-1">
                                <div class="py-2 px-2 badge rounded-circle bg-warning text-dark rounded"> </div>
                                Izin
                            </span>';
                } else {
                    return '<span class="me-4 d-flex align-items-center gap-1">
                            <div class="py-2 px-2 badge rounded-circle bg-secondary text-dark rounded"> </div>
                            Tida diketahui
                        </span>';
                }
            })
            ->addColumn('statusstring', function ($e) {
                if ($e->status == AttendanceStatusEnum::ACTIVE->value) {
                    return '<span class="me-4 d-flex align-items-center gap-1">
                                <div class="py-2 px-2 badge rounded-circle bg-success rounded"> </div>
                                ' . AttendanceStatusEnum::from($e->status)->label() . '
                            </span>';
                } else if ($e->status == AttendanceStatusEnum::PENDING->value) {
                    return '<span class="me-4 d-flex align-items-center gap-1">
                                <div class="py-2 px-2 badge rounded-circle bg-secondary rounded"> </div>
                                ' . AttendanceStatusEnum::from($e->status)->label() . '
                            </span>';
                } else if ($e->status == AttendanceStatusEnum::REJECT->value) {
                    $reason = $e->reason_revisi ? '<div class="alert alert-danger mt-3">' . $e->reason_revisi . '</div>' : '';
                    return '<span class="me-4 d-flex align-items-center gap-1">
                                <div class="py-2 px-2 badge rounded-circle bg-danger rounded"> </div>
                                ' . AttendanceStatusEnum::from($e->status)->label() . '
                            </span>'
                        . $reason;
                } else {
                    return '<span class="me-4 d-flex align-items-center gap-1">
                            <div class="py-2 px-2 badge rounded-circle bg-secondary text-dark rounded"> </div>
                            Tida diketahui
                        </span>';
                }
            })
            ->addColumn('btnaction', function ($e) {
                $btnApprove = $e->status == AttendanceStatusEnum::PENDING->value
                    ? '<button type="button" class="btn btn-sm btn-primary btnaction" data-type="approved" data-url="' . route('attendance.approved', ['attendance' => $e]) . '">Setujui</button>'
                    : '';

                $btnReject = $e->status == AttendanceStatusEnum::PENDING->value
                    ? '<button type="button" class="btn btn-sm btn-danger btnaction" data-type="reject" data-url="' . route('attendance.reject', ['attendance' => $e]) . '">Tolak</button>'
                    : '';

                return '
                    <div class="d-flex justify-content-between">
                        ' . $btnReject . ' ' . $btnApprove . '
                    </div>
                ';
            })
            ->rawColumns(['typestring', 'statusstring', 'btnaction'])
            ->make(true);
    }

    public function approved(Attendance $attendance)
    {
        try {
            $attendance->update([
                'status' => AttendanceStatusEnum::ACTIVE->value,
            ]);

            return ResponseClass::success();
        } catch (\Throwable $th) {
            return ResponseClass::error();
        }
    }

    public function reject(Attendance $attendance, Request $request)
    {
        try {
            $attendance->update([
                'status' => AttendanceStatusEnum::REJECT->value,
                'reason_revisi' => $request->reason,
            ]);

            return ResponseClass::success();
        } catch (\Throwable $th) {
            return ResponseClass::error();
        }
    }
}
