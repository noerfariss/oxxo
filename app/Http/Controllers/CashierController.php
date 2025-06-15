<?php

namespace App\Http\Controllers;

use App\Class\ResponseClass;
use App\Models\Category;
use App\Models\OutletKios;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
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

        $data = Product::query()
            ->with([
                'category:id,name'
            ])
            ->get();

        return ResponseClass::success(data: $data);
    }

    public function categories()
    {
        $data = Category::query()
            ->get();

        return ResponseClass::success(data: $data);
    }
}
