<!DOCTYPE html>
<html>

<head>
    <title>Invoice {{ $order->numberid }}</title>
    <style>
        @page {
            size: 120mm auto;
            /* kertas 12 cm */
            margin: 0;
        }

        body {
            font-family: "Courier New", Courier, monospace;
            font-size: 14px;
            /* 👉 ubah jadi 14px (bisa 16px kalau masih kecil) */
            width: 120mm;
            margin: 0 auto;
            letter-spacing: 0.9px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 3px 0;
        }

        /* padding diperbesar biar lebih lega */
        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="center">
        <div class="bold" style="font-size: 22px; margin-top:8mm">OXXO Care Cleaners</div>
        <div>{{ $order->kiostext->name }}</div>
        <div>{{ $order->kiostext->address }}</div>
        <hr>
        <div><b>{{ $order->numberid }}</b></div>
        <div>Masuk: {{ date('d/m/Y H:i', strtotime($order->orderin)) }}</div>
        <div>Keluar: {{ date('d/m/Y H:i', strtotime($order->orderout)) }}</div>
        <div>Pelanggan: {{ $order->membertext->name }}</div>
    </div>

    <hr>

    <table>
        <thead>
            <tr>
                <th>Qty</th>
                <th>Nama Item</th>
                <th class="right">Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->products as $item)
                <tr>
                    <td>{{ $item->quantity }}</td>
                    <td>
                        {{ $item->name }}
                        @if (!empty($item->noted))
                            <br><small><i>{{ $item->noted }}</i></small>
                        @endif
                    </td>
                    <td class="right">Rp {{ number_format($item->price) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr>

    <table>
        <tr>
            <td colspan="2" class="right">Subtotal:</td>
            <td class="right">Rp {{ number_format($order->subtotal) }}</td>
        </tr>
        <tr>
            <td colspan="2" class="right">Diskon:</td>
            <td class="right">{{ $order->discount }}%</td>
        </tr>
        <tr>
            <td colspan="2" class="right bold">Grand Total:</td>
            <td class="right bold">Rp {{ number_format($order->grandtotal) }}</td>
        </tr>
    </table>

    <hr>

    <div class="center">
        <div>Terima kasih telah menggunakan layanan kami.</div>
        <div>Barang telah dicek dan dikemas dengan baik.</div>
    </div>
</body>

</html>
