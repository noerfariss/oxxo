<?php

namespace App\Http\Controllers;

use App\Class\LogClass;
use App\Enums\UnitEnum;
use App\Http\Requests\Product\ProductCreateRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware('permission:PRODUCT_CREATE', only: ['create', 'store']),
            new Middleware('permission:PRODUCT_READ', only: ['index']),
            new Middleware('permission:PRODUCT_EDIT', only: ['edit', 'update']),
            new Middleware('permission:PRODUCT_DELETE', only: ['delete']),
        ];
    }
    public function index()
    {
        $title_page = 'Kategori';
        return view('member.product.index', compact('title_page'));
    }

    public function ajax(Request $request)
    {
        $cari = $request->cari;

        $data = Product::query()
            ->with([
                'category:id,name',
                'prices',
                'prices.attribute'
            ])
            ->when($cari, function ($e, $cari) {
                $e->where('name', 'like', "%{$cari}%");
            })
            ->where('status', cekStatus($request->status));

        return DataTables::eloquent($data)
            ->addColumn('pricestring', function ($e) {
                return $e->prices
                    ->map(fn($p) => optional($p->attribute)->name . ': ' . number_format($p->price, 0, ',', '.'))
                    ->implode(', ');
            })
            ->addColumn('aksi', function ($e) {
                $product = User::find(Auth::id());

                $btnEdit = $product->hasPermissionTo('PRODUCT_EDIT')
                    ? ($e->status == true ? '<li><a href="' . route('product.edit', ['product' => $e]) . '" class="dropdown-item"><i class="bx bx-pencil"></i> Edit</a></li>' : '')
                    : '';

                $btnDelete = $product->hasPermissionTo('PRODUCT_DELETE')
                    ? ($e->status == true ? '<li><a href="' . route('product.destroy', ['product' => $e]) . '" data-title="' . $e->name . '" class="dropdown-item btn-hapus"><i class="bx bx-trash"></i> Delete</a></li>' : '')
                    : '';

                $btnReload = $product->hasPermissionTo('PRODUCT_EDIT')
                    ? ($e->status == false ?  '<li><a href="' . route('product.destroy', ['product' => $e]) . '" data-title="' . $e->name . '" data-status="' . $e->status . '" class="dropdown-item btn-hapus"><i class="bx bx-refresh"></i></i> reset</a></li>' : '')
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
        $attribute = ProductAttribute::query()->where('status', true)->get();

        $units = [
            UnitEnum::PCS->label() => UnitEnum::PCS->value,
            UnitEnum::METER->label() => UnitEnum::METER->value
        ];

        return view('member.product.create', compact('title_page', 'attribute', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCreateRequest $request)
    {

        DB::beginTransaction();
        try {
            $product = Product::create($request->only(['name', 'category_id', 'unit']));

            foreach ($request->price as $key => $price) {
                DB::table('product_price')->insert([
                    'product_id' => $product->id,
                    'product_attribute_id' => $key,
                    'price' => $price,
                    'created_at' => Carbon::now()
                ]);
            }

            LogClass::set('Created Product: ' . $request->name);

            DB::commit();

            return redirect()->route('product.edit', ['product' => $product])->with('pesan', '<div class="alert alert-success">Data berhasil ditambahkan</div>');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::warning($th->getMessage());
            return redirect()->back()->with('pesan', '<div class="alert alert-danger">Terjadi kesalahan, cobalah kembali</div>');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $title_page = 'Edit Produk';

        $units = [
            UnitEnum::PCS->label() => UnitEnum::PCS->value,
            UnitEnum::METER->label() => UnitEnum::METER->value
        ];

        $attribute = DB::table('product_attributes as a')
            ->leftJoin('product_price as b', function ($leftJoin) use ($product) {
                $leftJoin->on('b.product_attribute_id', '=', 'a.id')->where('b.product_id', $product->id);
            })
            ->where('status', true)
            ->select(
                'a.id',
                'a.name',
                'b.product_id',
                DB::raw('COALESCE(b.price, 0) AS price')
            )
            ->get();

        return view('member.product.edit', compact('product', 'title_page', 'units', 'attribute'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        DB::beginTransaction();
        try {
            $product->update($request->only(['name', 'category_id', 'unit', 'status']));

            DB::table('product_price')->where('product_id', $product->id)->delete();
            foreach ($request->price as $key => $price) {
                DB::table('product_price')->insert([
                    'product_id' => $product->id,
                    'product_attribute_id' => $key,
                    'price' => $price,
                    'created_at' => Carbon::now()
                ]);
            }

            LogClass::set('Updated Product: ' . $request->name);

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
    public function destroy(Product $product)
    {
        $status = $product->status;
        DB::beginTransaction();
        try {
            if ($status == true) {
                $product->update(['status' => false]);
                LogClass::set('Disabled Product: ' . $product->email);
            } else {
                $product->update(['status' => true]);
                LogClass::set('Enabled Product: ' . $product->email);
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
