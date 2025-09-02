<?php

namespace App\Http\Controllers;

use App\Enums\OrderEnum;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderReportController extends Controller
{
    public function index()
    {
        return view('member.order.orderreport');
    }

    public function ajax(Request $request)
    {
        // dd($request->all());
        $search = $request->cari;

        // default status (kalau tidak ada type dikirim, misalnya dari datatable)
        switch ($request->type) {
            case 'new':
                $type = OrderEnum::NEW->value;
                break;

            case 'done':
                $type = OrderEnum::DONE->value;
                break;

            case 'out':
                $type = OrderEnum::OUT->value;
                break;

            default:
                $type = null;
        }

        $dates = pecahTanggal($request->dates);

        $data = Order::query()
            // searching
            ->when($search, function ($e, $search) {
                $e->where(function ($e) use ($search) {
                    $e->where('numberid', 'like', "%{$search}%")
                        ->orWhere('membertext->name', 'like', "%{$search}%")
                        ->orWhere('membertext->phone', 'like', "%{$search}%")
                        ->orWhere('membertext->address', 'like', "%{$search}%");
                });
            })
            // filter ordertype (checkbox)
            ->when($request->ordertype, function ($q, $ordertype) {
                $statusFilter = [];
                if (in_array('new', $ordertype)) {
                    $statusFilter[] = OrderEnum::NEW->value;
                }
                if (in_array('done', $ordertype)) {
                    $statusFilter[] = OrderEnum::DONE->value;
                }
                if (in_array('out', $ordertype)) {
                    $statusFilter[] = OrderEnum::OUT->value;
                }
                if (!empty($statusFilter)) {
                    $q->whereIn('status', $statusFilter);
                }
            })
            // filter paymenttype (checkbox)
            ->when($request->paymenttype, function ($q, $paymenttype) {
                $paymentFilter = [];
                if (in_array('outstanding', $paymenttype)) {
                    $paymentFilter[] = 'outstanding';
                }
                if (in_array('ppc', $paymenttype)) {
                    $paymentFilter[] = 'ppc';
                }
                if (in_array('cash', $paymenttype)) {
                    $paymentFilter[] = 'cash';
                }
                if (in_array('card', $paymenttype)) {
                    $paymentFilter[] = 'card';
                }
                if (!empty($paymentFilter)) {
                    $q->whereIn('payment_method', $paymentFilter);
                }
            })
            // filter tanggal
            ->when($dates, function ($q) use ($dates) {
                $q->whereBetween('created_at', [$dates[0] . ' 00:00:00', $dates[1] . ' 23:59:59']);
            });

        // contoh: kalau mau pakai labelreport (optional)
        // if ($request->labelreport) {
        //     // filter berdasarkan value select (new/done/out)
        //     $data->where('status', OrderEnum::tryFrom(strtoupper($request->labelreport))->value ?? null);
        // }

        return DataTables::eloquent($data)
            ->addColumn('idstatus', function ($e) {
                $id = $e->numberid;

                switch ($e->status) {
                    case OrderEnum::NEW->value:
                        $status = '<span class="badge bg-danger">DROP OFF</span>';
                        break;

                    case OrderEnum::DONE->value:
                        $status = '<span class="badge bg-success text-dark">DONE</span>';
                        break;

                    case OrderEnum::OUT->value:
                        $status = '<span class="badge bg-dark">PICKUP</span>';
                        break;
                }

                return "<b>{$id}</b><br/>{$status}";
            })
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
            ->addColumn('payment', fn($e) => ucfirst($e->payment_method))
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
            ->rawColumns(['idstatus', 'memberstring'])
            ->make(true);
    }


    public function report(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|string',
        ]);

        $dates = pecahTanggal($request->tanggal);

        // Decode array dari request
        $ordertype = json_decode($request->ordertype, true) ?? [];
        $paymenttype = json_decode($request->paymenttype, true) ?? [];

        // Base query
        $orders = Order::query()
            ->whereBetween('created_at', [$dates[0] . ' 00:00:00', $dates[1] . ' 23:59:59'])
            ->when($request->cari, function ($q, $cari) {
                $q->where(function ($q) use ($cari) {
                    $q->where('numberid', 'like', "%{$cari}%")
                        ->orWhere('membertext->name', 'like', "%{$cari}%")
                        ->orWhere('membertext->phone', 'like', "%{$cari}%")
                        ->orWhere('membertext->address', 'like', "%{$cari}%");
                });
            })
            ->when($ordertype, function ($q, $ordertype) {
                $statusFilter = [];
                if (in_array('new', $ordertype))  $statusFilter[] = OrderEnum::NEW->value;
                if (in_array('done', $ordertype)) $statusFilter[] = OrderEnum::DONE->value;
                if (in_array('out', $ordertype))  $statusFilter[] = OrderEnum::OUT->value;

                if (!empty($statusFilter)) {
                    $q->whereIn('status', $statusFilter);
                }
            })
            ->when($paymenttype, function ($q, $paymenttype) {
                $paymentFilter = [];
                if (in_array('outstanding', $paymenttype)) $paymentFilter[] = 'outstanding';
                if (in_array('ppc', $paymenttype))         $paymentFilter[] = 'ppc';
                if (in_array('cash', $paymenttype))        $paymentFilter[] = 'cash';
                if (in_array('card', $paymenttype))        $paymentFilter[] = 'card';

                if (!empty($paymentFilter)) {
                    $q->whereIn('payment_method', $paymentFilter);
                }
            });

        $orders = $orders->get();

        // Summary data
        $numClients = $orders->groupBy('member_id')->count();
        $numPcs = $orders->map(fn($o) => collect($o->products)->sum('quantity'))->sum();
        $grandTotal = $orders->sum('grandtotal');

        $totalPayment = $orders->groupBy('payment_method')->map(function ($group) {
            return [
                'count'  => $group->count(),
                'total'  => $group->sum(fn($item) => (float) $item['grandtotal']),
                'orders' => $group,
            ];
        });

        // Tentukan judul report
        $title = $request->labelreporttext ?: "Report Order";

        // Generate PDF
        $pdf = Pdf::loadView('member.order.reportpdf', [
            'ordertype' => $ordertype,
            'orders'        => $orders,
            'totalPayments' => $totalPayment,
            'startDate'     => $dates[0],
            'endDate'       => $dates[1],
            'numClients'    => $numClients,
            'numPcs'        => $numPcs,
            'grandTotal'    => $grandTotal,
            'title'         => $title,
        ]);

        return $pdf->stream("report_order_{$dates[0]}_{$dates[1]}.pdf");
    }


    public function print($id)
    {
        $order = Order::query()
            ->findOrFail($id);

        // dd($order->toArray());

        return view('member.order.orderprint', compact('order'));
    }
}
