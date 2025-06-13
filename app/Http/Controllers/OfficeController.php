<?php

namespace App\Http\Controllers;

use App\Class\LogClass;
use App\Http\Requests\Office\OfficeCreateRequest;
use App\Http\Requests\Office\OfficeUpdateRequest;
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

class OfficeController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:OFFICE_CREATE', only: ['create', 'store']),
            new Middleware('permission:OFFICE_READ', only: ['index']),
            new Middleware('permission:OFFICE_EDIT', only: ['edit', 'update']),
            new Middleware('permission:OFFICE_DELETE', only: ['delete']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title_page = 'Outlet';
        return view('member.office.index', compact('title_page'));
    }

    public function ajax(Request $request)
    {
        $cari = $request->cari;

        $data = Office::query()
            ->with('city')
            ->withCount(['kios'])
            ->when($cari, function ($e, $cari) {
                $e->where(function ($e) use ($cari) {
                    $e->where('name', 'like', "%{$cari}%")
                        ->orWhere('address', 'like', "%{$cari}%");
                });
            })
            ->where('status', cekStatus($request->status));

        return DataTables::eloquent($data)
            ->addColumn('aksi', function ($e) {
                $office = User::find(Auth::id());

                $btnEdit = $office->hasPermissionTo('OFFICE_EDIT')
                    ? ($e->status == true ? '<li><a href="' . route('office.edit', ['office' => $e]) . '" class="dropdown-item"><i class="bx bx-pencil"></i> Edit</a></li>' : '')
                    : '';

                $btnDelete = $office->hasPermissionTo('OFFICE_DELETE')
                    ? ($e->status == true ? '<li><a href="' . route('office.destroy', ['office' => $e]) . '" data-title="' . $e->name . '" class="dropdown-item btn-hapus"><i class="bx bx-trash"></i> Delete</a></li>' : '')
                    : '';

                $btnReload = $office->hasPermissionTo('OFFICE_EDIT')
                    ? ($e->status == false ?  '<li><a href="' . route('office.destroy', ['office' => $e]) . '" data-title="' . $e->name . '" data-status="' . $e->status . '" class="dropdown-item btn-hapus"><i class="bx bx-refresh"></i></i> reset</a></li>' : '')
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
            ->rawColumns(['aksi', 'statusstring', 'branchstring'])
            ->make(true);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title_page = 'Tambah Outlet/Cabang';
        return view('member.office.create', compact('title_page'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OfficeCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $office = Office::create($request->only(['name', 'address', 'city_id', 'latitude', 'longitude']));

            LogClass::set('Created Office: ' . $request->name);

            DB::commit();

            return redirect()->route('office.edit', ['office' => $office->uuid])->with('pesan', '<div class="alert alert-success">Data berhasil ditambahkan</div>');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::warning($th->getMessage());
            return redirect()->back()->with('pesan', '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Office $office)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Office $office)
    {
        $title_page = 'Edit Outlet';
        return view('member.office.edit', compact('office', 'title_page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OfficeUpdateRequest $request, Office $office)
    {
        DB::beginTransaction();
        try {
            Office::find($office->id)->update($request->only(['name', 'address', 'city_id', 'latitude', 'longitude', 'is_branch', 'status']));
            LogClass::set('Updated office: ' . $request->name);

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
    public function destroy(Office $office)
    {
        $status = $office->status;
        DB::beginTransaction();
        try {
            if ($status == true) {
                $office->update(['status' => false]);
                LogClass::set('Disabled office: ' . $office->name);
            } else {
                $office->update(['status' => true]);
                LogClass::set('Enabled office: ' . $office->name);
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



    // -==================== KIOSSSS ==================================
    public function outletkios(Office $office)
    {
        return view('member.office.outletkios', compact('office'));
    }

    public function outletkiosList(Request $request)
    {
        $search = $request->search;
        $data = OutletKios::query()
            ->when($search, fn($e) => $e->where('name', 'like', "%{$search}%"))
            ->whereNull('office_id')
            ->where('status', true);

        return DataTables::eloquent($data)
            ->addColumn('checkbox', fn($e) => "<input type='checkbox' name='id' value='{$e->id}' data-label='{$e->name}' />")
            ->addColumn('namefull', function ($e) {
                return "
                        <b>{$e->name}</b> <br/>
                        {$e->address} <br/>
                        <b>{$e->city->name}</b>
                ";
            })
            ->rawColumns(['checkbox', 'namefull'])
            ->make(true);
    }

    public function outletkiosUpdate(Request $request, Office $office)
    {
        DB::beginTransaction();
        try {
            OutletKios::where('office_id', $office->id)->update(['office_id' => null]);

            if ($request->modalkios) {
                foreach ($request->modalkios as $kios) {
                    OutletKios::where('id', $kios)->update(['office_id' => $office->id]);
                }
            }

            DB::commit();
            return redirect()->back()->with('pesan', '<div class="alert alert-success">Kios berhasil diperbarui</div>');
        } catch (\Throwable $th) {
            DB::rollBack();
            info($th->getMessage());

            return redirect()->back()->with('pesan', '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>');
        }
    }
}
