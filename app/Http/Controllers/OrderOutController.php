<?php

namespace App\Http\Controllers;

use App\Enums\OrderEnum;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderOutController extends Controller
{
    public function index()
    {
        return view('member.order.orderout');
    }

    public function ajax(Request $request)
    {
        $data = Order::query()
            ->where('status', OrderEnum::OUT->value);

        return DataTables::eloquent($data)
            ->addColumn('product_count', fn($e) => count($e->product_id))
            ->editColumn('subtotal', fn($e) => 'Rp ' . number_format($e->subtotal))
            ->editColumn('grandtotal', fn($e) => 'Rp ' . number_format($e->grandtotal))
            ->addColumn('qtytotal', fn($e) => collect($e->products)->sum('quantity'))
            ->addColumn('payment', fn($e) => 'Saldo')
            ->addColumn('customer', fn($e) => 'JEMS AINSTAIN')
            ->addColumn('aksi', function ($e) {
                return '<form method="POST" action="' . route('order.masuk', $e->id) . '" onsubmit="return confirm(\'Proses barang masuk?\')">' .
                    csrf_field() .
                    method_field('PUT') .
                    '<button type="submit" class="btn btn-sm btn-primary">Proses barang Masuk</button>' .
                    '</form>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function prosesIn($id)
    {
        $order = Order::findOrFail($id);
        $order->status = OrderEnum::IN->value;
        $order->orderin = Carbon::now();
        $order->save();

        return back()->with('pesan', '<div class="alert alert-success">Order berhasil diproses menjadi masuk.</div>');
    }

    public function report(Request $request)
    {
        $dates = pecahTanggal($request->tanggal);
        // Validasi tanggal input, wajib diisi dan format range tanggal "YYYY-MM-DD to YYYY-MM-DD"
        $request->validate([
            'tanggal' => 'required|string',
        ]);

        // Ambil tanggal dari input, formatnya dari flatpickr "YYYY-MM-DD to YYYY-MM-DD"
        $tanggal = explode(' to ', $request->tanggal);

        // Query order berdasarkan tanggal dan status NEW (bisa disesuaikan)
        $orders = Order::where('status', OrderEnum::OUT->value)
            ->whereDate('created_at', '>=', $dates[0])
            ->whereDate('created_at', '<=', $dates[1])
            ->get();

        $numClients = collect($orders)->groupBy('member_id')->count();
        $numPcs = collect($orders)->map(function ($items) {
            return collect($items->products)->sum('quantity');
        })->sum();

        $grandTotal = collect($orders)->sum('grandtotal');

        // Load view untuk report pdf, kirim data orders dan tanggal
        $pdf = Pdf::loadView('member.order.orderoutpdf', [
            'orders' => $orders,
            'startDate' => $dates[0],
            'endDate' => $dates[1],
            'numClients' => $numClients,
            'numPcs' => $numPcs,
            'grandTotal' => $grandTotal
        ]);

        // Download PDF dengan nama file report_order_YYYYMMDD_YYYYMMDD.pdf
        // return $pdf->download("report_order_{$startDate}_{$endDate}.pdf");
        return $pdf->stream("report_barang_keluar_{$dates[0]}_{$dates[1]}.pdf");
    }
}
