<?php

namespace App\Class;

use App\Models\Order;

class OrderClass
{

    public static function generateNumber($kios_id)
    {
        $now = now();
        $yearMonth = $now->format('Ym'); // Contoh: 202507
        $prefix = "BILL/{$kios_id}/{$yearMonth}";

        // Hitung jumlah order untuk kios ini dalam bulan ini
        $count = Order::where('kios_id', $kios_id)
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->count() + 1;

        // Nomor urut 4 digit
        $sequence = str_pad($count, 4, '0', STR_PAD_LEFT);

        return "{$prefix}/{$sequence}";
    }
}
