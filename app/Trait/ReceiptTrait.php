<?php

namespace App\Trait;

use Illuminate\Support\Facades\DB;

trait ReceiptTrait
{
    protected function checkReceipt($member_id)
    {
        $month = date('Y-m');

        $data = DB::table('receipts')
            ->whereRaw('DATE_FORMAT(dates, "%Y-%m") = ? ', [$month])
            ->where('member_id', $member_id)
            ->get();

        return $data;
    }
}
