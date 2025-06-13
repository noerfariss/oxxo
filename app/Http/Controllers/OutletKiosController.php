<?php

namespace App\Http\Controllers;

use App\Class\LogClass;
use App\Http\Requests\Kios\KiosCreateRequest;
use App\Http\Requests\Kios\KiosUpdateRequest;
use App\Models\Office;
use App\Models\OutletKios;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class OutletKiosController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:OUTLETKIOS_CREATE', only: ['create', 'store']),
            new Middleware('permission:OUTLETKIOS_READ', only: ['index']),
            new Middleware('permission:OUTLETKIOS_EDIT', only: ['edit', 'update']),
            new Middleware('permission:OUTLETKIOS_DELETE', only: ['delete']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title_page = 'Kios';
        return view('member.kios.index', compact('title_page'));
    }

    public function ajax(Request $request)
    {
        $cari = $request->cari;

        $data = OutletKios::query()
            ->with([
                'city',
                'office'
            ])
            ->when($cari, function ($e, $cari) {
                $e->where(function ($e) use ($cari) {
                    $e->where('name', 'like', "%{$cari}%")
                        ->orWhere('address', 'like', "%{$cari}%");
                });
            })
            ->where('status', cekStatus($request->status));

        return DataTables::eloquent($data)
            ->addColumn('outlet', fn($e) => $e->office ? $e->office->name : '')
            ->addColumn('aksi', function ($e) {
                $kios = User::find(Auth::id());

                $btnEdit = $kios->hasPermissionTo('OUTLETKIOS_EDIT')
                    ? ($e->status == true ? '<li><a href="' . route('kios.edit', ['kios' => $e]) . '" class="dropdown-item"><i class="bx bx-pencil"></i> Edit</a></li>' : '')
                    : '';

                $btnDelete = $kios->hasPermissionTo('OUTLETKIOS_DELETE')
                    ? ($e->status == true ? '<li><a href="' . route('kios.destroy', ['kios' => $e]) . '" data-title="' . $e->name . '" class="dropdown-item btn-hapus"><i class="bx bx-trash"></i> Delete</a></li>' : '')
                    : '';

                $btnReload = $kios->hasPermissionTo('OUTLETKIOS_EDIT')
                    ? ($e->status == false ?  '<li><a href="' . route('kios.destroy', ['kios' => $e]) . '" data-title="' . $e->name . '" data-status="' . $e->status . '" class="dropdown-item btn-hapus"><i class="bx bx-refresh"></i></i> reset</a></li>' : '')
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


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title_page = 'Tambah Kios';
        return view('member.kios.create', compact('title_page'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KiosCreateRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(OutletKios $kios)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OutletKios $kios)
    {
        return view('member.kios.edit', compact('kios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KiosUpdateRequest $request, OutletKios $kios)
    {
        DB::beginTransaction();
        try {
            $kios->update($request->only(['name','address','city_id','latitude','longitude','status','office_id']));

            DB::commit();
            return redirect()->back()->with('pesan', '<div class="alert alert-success">Kios berhasil diperbarui</div>');
        } catch (\Throwable $th) {
            DB::rollBack();
            info($th->getMessage());

            return redirect()->back()->with('pesan', '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>');
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OutletKios $kios)
    {
        $status = $kios->status;
        DB::beginTransaction();
        try {
            if ($status == true) {
                $kios->update(['status' => false]);
                LogClass::set('Disabled kios: ' . $kios->email);
            } else {
                $kios->update(['status' => true]);
                LogClass::set('Enabled kios: ' . $kios->email);
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


    // -=========== outlet / cabang ===============

    public function outletList(Request $request)
    {
        $search = $request->search;

        $data = Office::query()
            ->with('city')
            ->withCount(['kios'])
            ->when($search, fn($e, $search) => $e->where('name', 'like', "%{$search}%"))
            ->where('status', true);

        return DataTables::eloquent($data)
            ->addColumn('checkbox', fn($e) => "<input type='radio' name='outlet' value='{$e->id}' data-label='{$e->name}' />")
            ->addColumn('namefull', function ($e) {
                return "
                        <b>{$e->name}</b> <br/>
                        {$e->address} <br/>
                        <b>{$e->city->name}</b>
                ";
            })
            ->rawColumns(['branchstring', 'checkbox', 'namefull'])
            ->make(true);
    }
}
