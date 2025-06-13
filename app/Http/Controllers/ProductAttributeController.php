<?php

namespace App\Http\Controllers;

use App\Class\LogClass;
use App\Http\Requests\ProductAttribute\ProductAttributeCreateRequest;
use App\Http\Requests\ProductAttribute\ProductAttributeUpdateRequest;
use App\Models\ProductAttribute;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ProductAttributeController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:PRODUCTATTRIBUTE_CREATE', only: ['create', 'store']),
            new Middleware('permission:PRODUCTATTRIBUTE_READ', only: ['index']),
            new Middleware('permission:PRODUCTATTRIBUTE_EDIT', only: ['edit', 'update']),
            new Middleware('permission:PRODUCTATTRIBUTE_DELETE', only: ['delete']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title_page = 'Pengaturan Produk';
        return view('member.attribute.index', compact('title_page'));
    }

    public function ajax(Request $request)
    {
        $cari = $request->cari;

        $data = ProductAttribute::query()
            ->when($cari, function ($e, $cari) {
                $e->where('name', 'like', "%{$cari}%");
            })
            ->where('status', cekStatus($request->status));

        return DataTables::eloquent($data)
            ->addColumn('aksi', function ($e) {
                $productattribute = User::find(Auth::id());

                $btnEdit = $productattribute->hasPermissionTo('PRODUCTATTRIBUTE_EDIT')
                    ? ($e->status == true ? '<li><a href="' . route('productattribute.edit', ['productattribute' => $e]) . '" class="dropdown-item"><i class="bx bx-pencil"></i> Edit</a></li>' : '')
                    : '';

                $btnDelete = $productattribute->hasPermissionTo('PRODUCTATTRIBUTE_DELETE')
                    ? ($e->status == true ? '<li><a href="' . route('productattribute.destroy', ['productattribute' => $e]) . '" data-title="' . $e->name . '" class="dropdown-item btn-hapus"><i class="bx bx-trash"></i> Delete</a></li>' : '')
                    : '';

                $btnReload = $productattribute->hasPermissionTo('PRODUCTATTRIBUTE_EDIT')
                    ? ($e->status == false ?  '<li><a href="' . route('productattribute.destroy', ['productattribute' => $e]) . '" data-title="' . $e->name . '" data-status="' . $e->status . '" class="dropdown-item btn-hapus"><i class="bx bx-refresh"></i></i> reset</a></li>' : '')
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
        $title_page = 'Tambah Attribute';
        return view('member.attribute.create', compact('title_page'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductAttributeCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $productattribute = productattribute::create($request->only(['name']));

            LogClass::set('Created productattribute: ' . $request->name);

            DB::commit();

            return redirect()->route('productattribute.edit', ['productattribute' => $productattribute])->with('pesan', '<div class="alert alert-success">Data berhasil ditambahkan</div>');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::warning($th->getMessage());
            return redirect()->back()->with('pesan', '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(productattribute $productattribute)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductAttribute $productattribute)
    {
        $title_page = 'Edit Attribute';
        return view('member.attribute.edit', compact('productattribute', 'title_page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductAttributeUpdateRequest $request, productattribute $productattribute)
    {
        DB::beginTransaction();
        try {
            $productattribute->update($request->only(['name', 'status']));
            LogClass::set('Updated productattribute: ' . $request->name);

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
    public function destroy(ProductAttribute $productattribute)
    {
        $status = $productattribute->status;
        DB::beginTransaction();
        try {
            if ($status == true) {
                $productattribute->update(['status' => false]);
                LogClass::set('Disabled productattribute: ' . $productattribute->email);
            } else {
                $productattribute->update(['status' => true]);
                LogClass::set('Enabled productattribute: ' . $productattribute->email);
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
