<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderReportExport implements FromView, WithStyles
{
    protected $orders;
    protected $totalPayments;
    protected $dates;
    protected $numClients;
    protected $numPcs;
    protected $grandTotal;
    protected $title;
    protected $ordertype;


    public function __construct($orders, $totalPayments, $dates, $numClients, $numPcs, $grandTotal, $title, $ordertype)
    {
        $this->orders        = $orders;
        $this->totalPayments = $totalPayments;
        $this->dates         = $dates;
        $this->numClients    = $numClients;
        $this->numPcs        = $numPcs;
        $this->grandTotal    = $grandTotal;
        $this->title         = $title;
        $this->ordertype     = $ordertype;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        return view('member.order.reportexcel', [
            'ordertype'      => $this->ordertype,
            'orders'         => $this->orders,
            'totalPayments'  => $this->totalPayments,
            'startDate'      => $this->dates[0],
            'endDate'        => $this->dates[1],
            'numClients'     => $this->numClients,
            'numPcs'         => $this->numPcs,
            'grandTotal'     => $this->grandTotal,
            'title'          => $this->title,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2 => ['font' => ['bold' => true, 'size' => 24]], // judul
            3 => ['font' => ['bold' => true]], // header tabel
        ];
    }
}
