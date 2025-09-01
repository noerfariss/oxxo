<?php

namespace App\Http\Controllers;

use App\Class\DepositClass;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class MemberReportController extends Controller
{
    public function index()
    {
        return view('member.member.report');
    }

    public function ajax(Request $request)
    {
        $cari = $request->cari;
        $membertype = $request->member_filter;

        $data = Member::query()
            ->with([
                'deposit',
                'latestcutoff'
            ])
            // ->where('id', 1)
            ->where('is_member', true)
            ->when($cari, function ($e, $cari) {
                $e->where(function ($e) use ($cari) {
                    $e->where('name', 'like', '%' . $cari . '%')
                        ->orWhere('numberid', 'like', '%' . $cari . '%');
                });
            })
            ->when($membertype, fn($e) => $e->whereIn('is_member', $membertype))
            ->where('status', cekStatus($request->status));

        // dd($data->get()->toArray());

        return DataTables::eloquent($data)
            ->addColumn('lastest_saldo', fn($e) => DepositClass::saldo($e))
            ->addColumn('aksi', function ($e) {
                $member = User::find(Auth::id());

                $btnEdit = $member->hasPermissionTo('MEMBER_EDIT') && $e->status == true
                    ? '<a href="' . route('member.edit', ['member' => $e]) . '" class="action-btn text-dark d-flex align-items-center border rounded btn-sm "><i class="bx bx-pencil"></i> Edit</a>'
                    : '';

                $btnPassword = $member->hasPermissionTo('MEMBER_EDIT') && $e->status == true
                    ? '<a href="' . route('member.edit', ['member' => $e, 'password' => true]) . '" class="action-btn d-flex align-items-center text-dark border rounded btn-sm"><i class="bx bx-dialpad-alt"></i> Password</a>'
                    : '';

                $btnDelete = $member->hasPermissionTo('MEMBER_DELETE') && $e->status == true
                    ? '<a href="' . route('member.destroy', ['member' => $e]) . '" data-title="' . $e->name . '" class="action-btn d-flex align-items-center text-danger border rounded btn-sm btn-hapus"><i class="bx bx-trash"></i> Hapus</a>'
                    : '';

                $btnReload = $member->hasPermissionTo('MEMBER_EDIT') && $e->status == false
                    ? '<a href="' . route('member.destroy', ['member' => $e]) . '" data-title="' . $e->name . '" data-status="' . $e->status . '" class="action-btn border rounded btn-sm text-dark btn-hapus"><i class="bx bx-refresh"></i> Reset</a>'
                    : '';

                return '
                    <div class="d-flex flex-wrap justify-content-end gap-2">
                        ' . $btnEdit . '

                        ' . $btnDelete . '
                        ' . $btnReload . '
                    </div>
                ';
            })
            ->rawColumns(['aksi', 'statusstring', 'memberstring'])
            ->make(true);
    }
}
