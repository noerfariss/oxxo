<?php

namespace App\Http\Controllers;

use App\Class\LogClass;
use App\Http\Requests\Category\CategoryCreateRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:CATEGORY_CREATE', only: ['create', 'store']),
            new Middleware('permission:CATEGORY_READ', only: ['index']),
            new Middleware('permission:CATEGORY_EDIT', only: ['edit', 'update']),
            new Middleware('permission:CATEGORY_DELETE', only: ['delete']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title_page = 'Kategori';
        return view('member.category.index', compact('title_page'));
    }

    public function ajax(Request $request)
    {
        $cari = $request->cari;

        $data = Category::query()
            ->when($cari, function ($e, $cari) {
                $e->where('name', 'like', "%{$cari}%");
            })
            ->where('status', cekStatus($request->status));

        return DataTables::eloquent($data)
            ->addColumn('aksi', function ($e) {
                $category = User::find(Auth::id());

                $btnEdit = $category->hasPermissionTo('CATEGORY_EDIT')
                    ? ($e->status == true ? '<li><a href="' . route('category.edit', ['category' => $e]) . '" class="dropdown-item"><i class="bx bx-pencil"></i> Edit</a></li>' : '')
                    : '';

                $btnDelete = $category->hasPermissionTo('CATEGORY_DELETE')
                    ? ($e->status == true ? '<li><a href="' . route('category.destroy', ['category' => $e]) . '" data-title="' . $e->name . '" class="dropdown-item btn-hapus"><i class="bx bx-trash"></i> Delete</a></li>' : '')
                    : '';

                $btnReload = $category->hasPermissionTo('CATEGORY_EDIT')
                    ? ($e->status == false ?  '<li><a href="' . route('category.destroy', ['category' => $e]) . '" data-title="' . $e->name . '" data-status="' . $e->status . '" class="dropdown-item btn-hapus"><i class="bx bx-refresh"></i></i> reset</a></li>' : '')
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
        $title_page = 'Tambah Kategori';
        return view('member.category.create', compact('title_page'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $category = Category::create($request->only(['name']));

            LogClass::set('Created category: ' . $request->name);

            DB::commit();

            return redirect()->route('category.edit', ['category' => $category])->with('pesan', '<div class="alert alert-success">Data berhasil ditambahkan</div>');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::warning($th->getMessage());
            return redirect()->back()->with('pesan', '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $title_page = 'Edit Kategori';
        return view('member.category.edit', compact('category', 'title_page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        DB::beginTransaction();
        try {
            $category->update($request->only(['name', 'status']));
            LogClass::set('Updated category: ' . $request->name);

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
    public function destroy(Category $category)
    {
        $status = $category->status;
        DB::beginTransaction();
        try {
            if ($status == true) {
                $category->update(['status' => false]);
                LogClass::set('Disabled category: ' . $category->email);
            } else {
                $category->update(['status' => true]);
                LogClass::set('Enabled category: ' . $category->email);
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
