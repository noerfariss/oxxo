<?php

namespace App\Http\Controllers;

use App\Enums\OrderEnum;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
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
        $type = $request->type == 'out' ? OrderEnum::OUT->value : OrderEnum::NEW->value;
        $dates = pecahTanggal($request->dates);

        $data = Order::query()
            ->where('status', $type)
            ->whereBetween('created_at', [$dates[0] . ' 00:00:00', $dates[1] . ' 23:59:59']);

        return DataTables::eloquent($data)
            ->addColumn('product_count', fn($e) => count($e->product_id))
            ->addColumn('subtotaltext', fn($e) => 'Rp ' . number_format($e->subtotal))
            ->editColumn('grandtotal', fn($e) => 'Rp ' . number_format($e->grandtotal))
            ->addColumn('qtytotal', fn($e) => collect($e->products)->sum('quantity'))
            ->addColumn('payment', fn($e) => 'Saldo')
            ->addColumn('discount_nominal', function ($e) {
                if ($e->discount_type === 'nominal') {
                    return 'Rp ' . number_format($e->discount);
                } else {
                    $diskon = ((int) $e->discount / 100) * (int) $e->subtotal;
                    return 'Rp ' . number_format($diskon);
                }
            })
            ->addColumn('discount_if_persen', function ($e) {
                if ($e->discount_type === 'persen') {
                    return '(' . number_format($e->discount, 1) . '%)';
                } else {
                    return '';
                }
            })
            // ->rawColumns(['aksi'])
            ->make(true);
    }

    public function prosesKeluar($id)
    {
        $order = Order::findOrFail($id);
        $order->status = OrderEnum::OUT->value;
        $order->orderout = Carbon::now();
        $order->save();

        return back()->with('pesan', '<div class="alert alert-success">Order berhasil diproses menjadi keluar.</div>');
    }

    public function print($id)
    {
        $order = Order::query()
            ->findOrFail($id);

        // dd($order->toArray());

        return view('member.order.orderprint', compact('order'));
    }

    public function report(Request $request)
    {
        $type = $request->type == strtolower(OrderEnum::OUT->label()) ? OrderEnum::OUT->value : OrderEnum::NEW->value;
        $dates = pecahTanggal($request->tanggal);
        // Validasi tanggal input, wajib diisi dan format range tanggal "YYYY-MM-DD to YYYY-MM-DD"
        $request->validate([
            'tanggal' => 'required|string',
        ]);

        // Ambil tanggal dari input, formatnya dari flatpickr "YYYY-MM-DD to YYYY-MM-DD"
        $tanggal = explode(' to ', $request->tanggal);

        // Query order berdasarkan tanggal dan status NEW (bisa disesuaikan)
        $orders = Order::where('status', $type)
            ->whereDate('created_at', '>=', $dates[0])
            ->whereDate('created_at', '<=', $dates[1])
            ->get();

        $numClients = collect($orders)->groupBy('member_id')->count();
        $numPcs = collect($orders)->map(function ($items) {
            return collect($items->products)->sum('quantity');
        })->sum();

        $grandTotal = collect($orders)->sum('grandtotal');

        // Load view untuk report pdf, kirim data orders dan tanggal
        $pdf = Pdf::loadView('member.order.orderpdf', [
            'orders' => $orders,
            'startDate' => $dates[0],
            'endDate' => $dates[1],
            'numClients' => $numClients,
            'numPcs' => $numPcs,
            'grandTotal' => $grandTotal,
            // 'logo' =>
            'title' => $request->type == strtolower(OrderEnum::OUT->label()) ? 'Report Pickup (Barang Keluar)' : 'Report Drop Off (Barang Masuk)',
        ]);

        // Download PDF dengan nama file report_order_YYYYMMDD_YYYYMMDD.pdf
        // return $pdf->download("report_order_{$startDate}_{$endDate}.pdf");
        return $pdf->stream("report_order_{$dates[0]}_{$dates[1]}.pdf");
    }

    // ------------------ barang keluar (pickup) --------------------


    public function out()
    {
        return view('member.order.orderout');
    }
}
