<?php

namespace App\Http\Controllers;

use App\Class\LogClass;
use App\Http\Requests\User\PasswordUserUpdateRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:USERWEB_CREATE', only: ['create', 'store']),
            new Middleware('permission:USERWEB_READ', only: ['index']),
            new Middleware('permission:USERWEB_EDIT', only: ['edit', 'update']),
            new Middleware('permission:USERWEB_DELETE', only: ['delete']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title_page = 'User';
        return view('member.user.index', compact('title_page'));
    }


    public function ajax(Request $request)
    {
        $cari = $request->cari;

        $data = User::query()
            ->when($cari, function ($e, $cari) {
                $e->where(function ($e) use ($cari) {
                    $e->where('name', 'like', '%' . $cari . '%')
                        ->orWhere('email', 'like', '%' . $cari . '%')
                        ->orWhere('whatsapp', 'like', '%' . $cari . '%');
                });
            })
            ->where('id', '<>', 1)
            ->where('status', cekStatus($request->status));

        return DataTables::eloquent($data)
            ->addColumn('aksi', function ($e) {
                $user = User::find(Auth::id());

                $btnEdit = $user->hasPermissionTo('USERWEB_EDIT')
                    ? ($e->status == true ? '<li><a href="' . route('user.edit', ['user' => $e]) . '" class="dropdown-item"><i class="bx bx-pencil"></i> Edit</a></li>' : '')
                    : '';

                $btnPassword = $user->hasPermissionTo('USERWEB_EDIT')
                    ? ($e->status == true ? '<li><a href="' . route('user.edit', ['user' => $e, 'password' => true]) . '" class="dropdown-item"><i class="bx bx-dialpad-alt"></i> Ganti Password</a></li>' : '')
                    : '';

                $btnDelete = $user->hasPermissionTo('USERWEB_DELETE')
                    ? ($e->status == true ? '<li><a href="' . route('user.destroy', ['user' => $e]) . '" data-title="' . $e->name . '" class="dropdown-item btn-hapus"><i class="bx bx-trash"></i> Delete</a></li>' : '')
                    : '';

                $btnReload = $user->hasPermissionTo('USERWEB_EDIT')
                    ? ($e->status == false ?  '<li><a href="' . route('user.destroy', ['user' => $e]) . '" data-title="' . $e->name . '" data-status="' . $e->status . '" class="dropdown-item btn-hapus"><i class="bx bx-refresh"></i></i> reset</a></li>' : '')
                    : '';

                return '<div class="btn-group float-end" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="badge border text-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    setting
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1">
                                    ' . $btnEdit . '
                                    ' . $btnPassword . '
                                    ' . $btnDelete . '
                                    ' . $btnReload . '
                                </ul>
                            </div>
                        </div>';
            })
            ->rawColumns(['aksi', 'statusstring'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title_page = 'Tambah User';
        $roles = Role::where('id', '<>', 1)->get();

        return view('member.user.create', compact('roles', 'title_page'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create($request->only(['name', 'email', 'whatsapp', 'password', 'photo', 'address']));
            $roles = Role::where('id', $request->roles)->get();
            $user->syncRoles($roles);

            LogClass::set('Created user: ' . $request->email);

            DB::commit();

            return redirect()->route('user.edit', ['user' => $user])->with('pesan', '<div class="alert alert-success">Data berhasil ditambahkan</div>');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::warning($th->getMessage());
            return redirect()->back()->with('pesan', '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>');
        }
    }

    public function password(PasswordUserUpdateRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            $user = User::find($user->id)->update([
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

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $title_page = 'Edit User';
        $roles = Role::where('id', '<>', 1)->get();

        if (request()->password) {
            return view('member.user.password', compact('user'));
        }

        return view('member.user.edit', compact('user', 'roles', 'title_page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            $user->update($request->only(['name', 'email', 'whatsapp', 'address', 'photo']));
            $roles = Role::where('id', $request->roles)->get();
            $user->syncRoles($roles);

            LogClass::set('Updated user: ' . $request->email);

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
    public function destroy(User $user)
    {
        $status = $user->status;
        DB::beginTransaction();
        try {
            if ($status == true) {
                $user->update(['status' => false]);
                LogClass::set('Disabled user: ' . $user->email);
            } else {
                $user->update(['status' => true]);
                LogClass::set('Enabled user: ' . $user->email);
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
}
