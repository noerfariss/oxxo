<?php

namespace App\Http\Controllers;

use App\Class\LogClass;
use App\Http\Requests\Remark\RemarkAttributeCreateRequest;
use App\Http\Requests\Remark\RemarkAttributeUpdateRequest;
use App\Models\Remark;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class RemarkController extends Controller  implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:REMARK_CREATE', only: ['create', 'store']),
            new Middleware('permission:REMARK_READ', only: ['index']),
            new Middleware('permission:REMARK_EDIT', only: ['edit', 'update']),
            new Middleware('permission:REMARK_DELETE', only: ['delete']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title_page = 'Pengaturan Produk';
        return view('member.remark.index', compact('title_page'));
    }

    public function ajax(Request $request)
    {
        $cari = $request->cari;

        $data = Remark::query()
            ->when($cari, function ($e, $cari) {
                $e->where('name', 'like', "%{$cari}%");
            })
            ->where('status', cekStatus($request->status));

        return DataTables::eloquent($data)
            ->addColumn('aksi', function ($e) {
                $remark = User::find(Auth::id());

                $btnEdit = $remark->hasPermissionTo('REMARK_EDIT')
                    ? ($e->status == true ? '<li><a href="' . route('remark.edit', ['remark' => $e]) . '" class="dropdown-item"><i class="bx bx-pencil"></i> Edit</a></li>' : '')
                    : '';

                $btnDelete = $remark->hasPermissionTo('REMARK_DELETE')
                    ? ($e->status == true ? '<li><a href="' . route('remark.destroy', ['remark' => $e]) . '" data-title="' . $e->name . '" class="dropdown-item btn-hapus"><i class="bx bx-trash"></i> Delete</a></li>' : '')
                    : '';

                $btnReload = $remark->hasPermissionTo('REMARK_EDIT')
                    ? ($e->status == false ?  '<li><a href="' . route('remark.destroy', ['remark' => $e]) . '" data-title="' . $e->name . '" data-status="' . $e->status . '" class="dropdown-item btn-hapus"><i class="bx bx-refresh"></i></i> reset</a></li>' : '')
                    : '';

                return '<div class="btn-group float-end" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="badge border text-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    setting
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1">
                                    ' . $btnEdit . '
                                    ' . $btnDelete . '
                                    ' . $btnReload . '
                                </ul>
                            </div>
                        </div>';
            })
            ->rawColumns(['aksi', 'statusstring'])
            ->make(true);
    }


    public function create()
    {
        $title_page = 'Tambah remark';
        return view('member.remark.create', compact('title_page'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RemarkAttributeCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $remark = Remark::create($request->only(['name']));

            LogClass::set('Created remark: ' . $request->name);

            DB::commit();

            return redirect()->route('remark.edit', ['remark' => $remark])->with('pesan', '<div class="alert alert-success">Data berhasil ditambahkan</div>');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::warning($th->getMessage());
            return redirect()->back()->with('pesan', '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Remark $remark)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Remark $remark)
    {
        $title_page = 'Edit remark';
        return view('member.remark.edit', compact('remark', 'title_page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RemarkAttributeUpdateRequest $request, Remark $remark)
    {
        DB::beginTransaction();
        try {
            $remark->update($request->only(['name', 'status']));
            LogClass::set('Updated remark: ' . $request->name);

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
    public function destroy(Remark $remark)
    {
        $status = $remark->status;
        DB::beginTransaction();
        try {
            if ($status == true) {
                $remark->update(['status' => false]);
                LogClass::set('Disabled remark: ' . $remark->name);
            } else {
                $remark->update(['status' => true]);
                LogClass::set('Enabled remark: ' . $remark->name);
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
