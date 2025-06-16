<?php

namespace App\Http\Controllers;

use App\Class\ResponseClass;
use App\Models\Category;
use App\Models\Order;
use App\Models\OutletKios;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CashierController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:CASHIER_CREATE', only: ['create', 'store']),
            new Middleware('permission:CASHIER_READ', only: ['index']),
            new Middleware('permission:CASHIER_EDIT', only: ['edit', 'update']),
            new Middleware('permission:CASHIER_DELETE', only: ['delete']),
        ];
    }

    public function index()
    {
        $data = OutletKios::query()
            ->where('status', true)
            ->get();

        return view('member.cashier.index', compact('data'));
    }

    public function cashier(OutletKios $kios)
    {
        return view('member.cashier.cashier');
    }

    public function items(Request $request)
    {
        $category = $request->category;
        $search = $request->search;
        $slug = $request->slug;

        $kios = OutletKios::where('uuid', $slug)->first();
        if (!$kios) {
            return ResponseClass::error('Data tidak ditemukan', statusCode: 404);
        }

        $data = DB::table('products as a')
            ->join('categories as b', 'b.id', '=', 'a.category_id')
            ->join('product_price as c', 'c.product_id', '=', 'a.id')
            ->join('product_attributes as d', 'd.id', '=', 'c.product_attribute_id')
            ->leftJoin('outlet_kios_product as e', function ($join) use ($kios) {
                $join->on('e.product_id', '=', 'a.id')
                    ->on('e.product_attribute_id', '=', 'c.product_attribute_id')
                    ->where('e.kios_id', '=', $kios->id);
            })
            ->when($category, fn($e) => $e->where('b.id', $category))
            ->when($search, fn($e) => $e->where('a.name', 'like', "%{$search}%"))
            ->select(
                'a.id',
                'b.name as category',
                'a.name',
                'd.name as attribute',
                'c.price as master_price',
                DB::raw('COALESCE(e.price, c.price) as price')
            )
            ->get();

        $collection = collect($data)->groupBy('id')->map(function ($data, $id) {
            $first = $data->first();

            return [
                'id' => $id,
                'category' => $first->category,
                'name' => $first->name,
                'attribute' => $data->map(function ($attribute) {
                    return [
                        'name' => $attribute->attribute,
                        'master_price' => $attribute->master_price,
                        'price' => $attribute->price,
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        return ResponseClass::success(data: $collection);
    }

    public function categories()
    {
        $data = Category::query()
            ->get();

        return ResponseClass::success(data: $data);
    }

    public function save(Request $request)
    {
        $slug = $request->slug;
        $cart = $request->cart;
        $kios = OutletKios::where('uuid', $slug)->first();

        $productId = collect($cart)->pluck('id')->toArray();

        DB::beginTransaction();
        try {
            Order::create([
                'kios_id' => $kios->id,
                'kiostext' => $kios,
                'product_id' => $productId,
                'products' => $cart,
            ]);

            DB::commit();

            return ResponseClass::success();
        } catch (\Throwable $th) {
            DB::rollBack();
            info($th->getMessage());
            return ResponseClass::error();
        }
    }
}
