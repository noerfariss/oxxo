<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\RoleCreateRequest;
use App\Http\Requests\Role\RoleEditRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:ROLE_CREATE', only: ['create', 'store']),
            new Middleware('permission:ROLE_READ', only: ['index']),
            new Middleware('permission:ROLE_EDIT', only: ['edit', 'update']),
            new Middleware('permission:ROLE_DELETE', only: ['delete']),
        ];
    }

    public function index()
    {
        $title_page = 'Role';
        return view('member.roles.index', compact('title_page'));
    }


    public function ajax(Request $request)
    {
        $cari = $request->cari;

        $data = Role::query()
            ->when($cari, function ($e, $cari) {
                $e->where('name', 'like', '%' . $cari . '%');
            })
            ->where('id', '<>', 1);

        return DataTables::eloquent($data)
            ->addColumn('aksi', function ($e) {
                $user = User::find(Auth::id());

                $btnEdit = $user->hasPermissionTo('ROLE_EDIT')
                    ? '<li><a href="' . route('role.edit', ['role' => $e->id]) . '" class="dropdown-item"><i class="bx bx-pencil"></i> Edit</a></li>' : '';

                $btnDelete = $user->hasPermissionTo('ROLE_DELETE')
                    ? '<li><a href="' . route('role.destroy', ['role' => $e->id]) . '" data-title="' . $e->name . '" class="dropdown-item btn-hapus"><i class="bx bx-trash"></i> Delete</a></li>' : '';

                return '<div class="btn-group float-end" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="badge border text-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    setting
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1">
                                    ' . $btnEdit . '
                                    ' . $btnDelete . '
                                </ul>
                            </div>
                        </div>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function create()
    {
        $title_page = 'Tambah Role';
        return view('member.roles.create', compact('title_page'));
    }

    public function store(RoleCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $role = Role::create($request->only(['name']));

            $permission = Permission::whereIn('id', $request->permission)->get();
            $role->syncPermissions($permission);

            DB::commit();

            return redirect()->route('role.edit', ['role' => $role])->with('pesan', '<div class="alert alert-success">Data berhasil ditambahkan</div>');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::warning($th->getMessage());
            return redirect()->back()->with('pesan', '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>');
        }
    }


    public function show($role) {}

    public function edit(Role $role)
    {
        $title_page = 'Edit Role';
        return view('member.roles.edit', compact('role', 'title_page'));
    }

    public function update(RoleEditRequest $request, Role $role)
    {
        DB::beginTransaction();
        try {
            $role->update($request->only(['name']));

            $permission = Permission::whereIn('id', $request->permission)->get();
            $role->syncPermissions($permission);

            DB::commit();

            return redirect()->back()->with('pesan', '<div class="alert alert-success">Data berhasil diperbaruhi</div>');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::warning($th->getMessage());

            return redirect()->back()->with('pesan', '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>');
        }
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return response()->json([
                'pesan' => 'Role <b>' . $role->name . '</b> tidak bisa dihapus, karena masih digunakan <b>' . $role->users()->count() . ' user</b>'
            ], 500);
        }

        DB::beginTransaction();
        try {
            $role->delete();
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
