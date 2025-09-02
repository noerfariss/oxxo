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
        $search = $request->cari;

        switch ($request->type) {
            case 'new':
                $type =  OrderEnum::NEW->value;
                break;

            case 'done':
                $type =  OrderEnum::DONE->value;
                break;

            case 'out':
                $type =  OrderEnum::OUT->value;
                break;
        }

        $dates = pecahTanggal($request->dates);

        $data = Order::query()
            ->when($search, function ($e, $search) {
                $e->where(function ($e) use ($search) {
                    $e->where('numberid', 'like', "%{$search}%")
                        ->orWhere('membertext->name', 'like', "%{$search}%")
                        ->orWhere('membertext->phone', 'like', "%{$search}%")
                        ->orWhere('membertext->address', 'like', "%{$search}%");
                });
            })
            ->where('status', $type)
            ->whereBetween('created_at', [$dates[0] . ' 00:00:00', $dates[1] . ' 23:59:59']);

        return DataTables::eloquent($data)
            ->addColumn('memberstring', function ($e) {
                $member = $e->membertext;
                $name = $member->name;
                $phone = $member->phone;
                $address = $member->address;

                return $name . ' - ' . $phone . '<br/><small>' . $address . '</small>';
            })
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
            ->rawColumns(['memberstring'])
            ->make(true);
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
        switch ($request->type) {
            case 'new':
                $type =  OrderEnum::NEW->value;
                $title = 'Report Drop Off (Barang Masuk)';
                break;

            case 'done':
                $type =  OrderEnum::DONE->value;
                $title = 'Report Stock Opname';
                break;

            case 'out':
                $type =  OrderEnum::OUT->value;
                $title = 'Report Pickup (Barang Keluar)';
                break;
        }

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
            ->whereDate('created_at', '<=', $dates[1]);

        // Cek checkbox is_outstanding
        if ($request->boolean('is_outstanding')) {
            // Include cash, card, outstanding
            $orders->whereIn('payment_method', ['cash', 'card', 'outstanding', 'ppc']);
        } else {
            // Hanya cash dan card
            $orders->whereIn('payment_method', ['cash', 'card', 'ppc']);
        }

        $orders = $orders->get();

        $numClients = collect($orders)->groupBy('member_id')->count();
        $numPcs = collect($orders)->map(function ($items) {
            return collect($items->products)->sum('quantity');
        })->sum();

        $grandTotal = collect($orders)->sum('grandtotal');

        // total by payment method
        $totalPayment = collect($orders) // $orders dari query kamu
            ->groupBy('payment_method')
            ->map(function ($group) {
                return [
                    'count'      => $group->count(),
                    'total'      => $group->sum(fn($item) => (float) $item['grandtotal']),
                    'orders'     => $group, // kalau mau lihat detail tiap order
                ];
            });

        // Load view untuk report pdf, kirim data orders dan tanggal
        $pdf = Pdf::loadView('member.order.orderpdf', [
            'orders' => $orders,
            'totalPayments' => $totalPayment,
            'startDate' => $dates[0],
            'endDate' => $dates[1],
            'numClients' => $numClients,
            'numPcs' => $numPcs,
            'grandTotal' => $grandTotal,
            'type' => $type,
            'title' => $title,
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
