<?php

namespace App\Http\Controllers;

use App\Class\LogClass;
use App\Events\LogEvent;
use App\Http\Requests\Member\MemberCreateRequest;
use App\Http\Requests\Member\MemberUpdateRequest;
use App\Http\Requests\Member\PasswordUpdateRequest;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class MemberController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:MEMBER_CREATE', only: ['create', 'store']),
            new Middleware('permission:MEMBER_READ', only: ['index']),
            new Middleware('permission:MEMBER_EDIT', only: ['edit', 'update']),
            new Middleware('permission:MEMBER_DELETE', only: ['delete']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('member.member.index');
    }

    public function ajax(Request $request)
    {
        $cari = $request->cari;
        $office = $request->office;
        $membertype = $request->member_filter;

        $data = Member::query()
            ->when($cari, function ($e, $cari) {
                $e->where(function ($e) use ($cari) {
                    $e->where('name', 'like', "%{$cari}%")
                        ->orWhere('numberid', 'like', "%{$cari}%")
                        ->orWhere('phone', 'like', "%{$cari}%");
                });
            })
            ->when($membertype, fn($e) => $e->whereIn('is_member', $membertype))
            ->where('status', cekStatus($request->status));

        return DataTables::eloquent($data)
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('member.member.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MemberCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $member = Member::create($request->only(['office_id', 'kios_id', 'numberid', 'name', 'phone', 'address', 'born', 'city_id', 'gender', 'password', 'is_member']));

            LogClass::set('Created member: ' . $request->name);

            DB::commit();

            return redirect()->route('member.edit', ['member' => $member])->with('pesan', '<div class="alert alert-success">Data berhasil ditambahkan</div>');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::warning($th->getMessage());
            return redirect()->back()->with('pesan', '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        if (request()->password) {
            return view('member.member.password', compact('member'));
        }

        return view('member.member.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MemberUpdateRequest $request, Member $member)
    {
        DB::beginTransaction();
        try {
            $member->update($request->only(['office_id', 'kios_id', 'numberid', 'name', 'phone', 'address', 'born', 'city_id', 'gender', 'is_member', 'status']));
            LogClass::set('Updated member: ' . $request->name);

            DB::commit();

            return redirect()->back()->with('pesan', '<div class="alert alert-success">Data berhasil diperbaruhi</div>');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::warning($th->getMessage());

            return redirect()->back()->with('pesan', '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $status = $member->status;

        DB::beginTransaction();
        try {
            if ($status == true) {
                $member->update(['status' => false]);
                LogClass::set('Disabled member: ' . $member->name);
            } else {
                $member->update(['status' => true]);
                LogClass::set('Enabled member: ' . $member->name);
            }

            DB::commit();

            return response()->json([
                'pesan' => 'Data berhasil dihapus',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::warning($th->getMessage());
            return response()->json([
                'pesan' => 'Terjadi kesalahan'
            ], 500);
        }
    }

    public function password(PasswordUpdateRequest $request, Member $member)
    {
        DB::beginTransaction();
        try {
            $member->update([
                'password' => Hash::make($request->password)
            ]);

            LogClass::set('Change password');

            DB::commit();

            return redirect()->back()->with('pesan', '<div class="alert alert-success">Passsowrd berhasil diperbarui</div>');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::warning($th->getMessage());
            return redirect()->back()->with('pesan', '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>');
        }
    }
}
