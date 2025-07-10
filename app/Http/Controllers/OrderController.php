<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function index()
    {
        return view('member.order.index');
    }

    public function ajax(Request $request)
    {
        $data = Order::query();

        // dd($data->get()->toArray());

        return DataTables::eloquent($data)
            ->addColumn('product_count', fn($e) => count($e->product_id))
            ->editColumn('subtotal', fn($e) => 'Rp '.number_format($e->subtotal))
            ->editColumn('grandtotal', fn($e) => 'Rp ' . number_format($e->grandtotal))
            ->addColumn('qtytotal', fn($e) => collect($e->products)->sum('quantity'))
            ->addColumn('payment', fn($e) => 'Saldo')
            ->addColumn('customer', fn($e) => 'JEMS AINSTAIN')
            ->make(true);
    }
}
