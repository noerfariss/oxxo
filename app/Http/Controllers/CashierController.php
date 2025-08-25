<?php

namespace App\Http\Controllers;

use App\Class\DepositClass;
use App\Class\OrderClass;
use App\Class\ResponseClass;
use App\Enums\DepositEnum;
use App\Models\Category;
use App\Models\Deposit;
use App\Models\Member;
use App\Models\Order;
use App\Models\OutletKios;
use App\Models\Product;
use App\Models\Remark;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
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
        return view('member.cashier.cashier', compact('kios'));
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

    public function remaks()
    {
        $data = Remark::query()
            ->where('status', true)
            ->get();

        return ResponseClass::success(data: $data);
    }

    public function setting()
    {
        $data = Setting::query()
            ->first();

        return ResponseClass::success(data: $data);
    }

    public function save(Request $request)
    {
        $slug = $request->slug;
        $cart = $request->cart;
        $member = $request->member;
        $discount = $request->discount;
        $typeDiskon = $request->typeDiskon;
        $membertext = Member::find($member);

        $kios = OutletKios::where('uuid', $slug)->first();

        $productId = collect($cart)->pluck('id')->toArray();

        $subtotal = collect($cart)->sum('subtotal');
        $discountrupiah = $typeDiskon == 'persen'
            ? ((int) $discount > 0 ? ($discount / 100) * $subtotal : 0)
            : (int) $discount;

        $grandtotal = $subtotal - $discountrupiah;

        $paymentMethod = $request->paymentMethod;

        DB::beginTransaction();
        try {
            Order::create([
                'numberid' => OrderClass::generateNumber($kios->id),
                'kios_id' => $kios->id,
                'kiostext' => $kios,
                'product_id' => $productId,
                'products' => $cart,
                'member_id' => $member,
                'membertext' => $membertext,
                'subtotal' => $subtotal,
                'discount_type' => $typeDiskon,
                'discount' => $discount,
                'grandtotal' => $grandtotal,
                'payment_method' => $paymentMethod,
            ]);

            if($paymentMethod === 'ppc'){
                Deposit::create([
                    'dates' => Carbon::now(),
                    'member_id' => $member,
                    'type' => DepositEnum::OUT->value,
                    'amount' => $grandtotal,
                    'note' => 'Pembayaran'
                ]);
            }

            DB::commit();

            return ResponseClass::success();
        } catch (\Throwable $th) {
            DB::rollBack();
            info($th->getMessage());
            return ResponseClass::error();
        }
    }

    public function customers(Request $request)
    {
        $search = $request->query('q');
        $query = Member::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $customers = $query->limit(10)->get();

        // dd($customers->toArray());

        return response()->json($customers->map(function ($c) {
            return [
                'value' => $c->id,
                'label' => $c->name,
                'saldo' => DepositClass::saldo($c)
            ];
        }));
    }
}
