<!DOCTYPE html>
<html>

<head>
    <title>Report Order {{ $startDate }} s/d {{ $endDate }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            /* width: 100%; */
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            /* border: 1px solid #000; */
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        h3 {
            text-align: center;
        }
    </style>
</head>

<body>
    <table width="100%">
        <tr>
            <td style="width: 150px; text-align:center;">
                <img src="{{ public_path('images/logo.png') }}" style="width: 120px;">
            </td>
            <td style="text-align: center;">
                <h2 style="padding: 0; margin:0">{{ $title }}</h2>
                <p style="padding:0; margin:0;">Periode: {{ $startDate }} s/d {{ $endDate }}</p>
            </td>
            <td style="width: 150px;"></td>
        </tr>
    </table>

    <table border="1" width="100%">
        <thead>
            <tr>
                <th>No. Bill</th>
                <th>ID</th>
                <th>Customer</th>
                <th style="text-align: center;">Items</th>
                <th style="text-align: right;">Grandtotal</th>
                <th>Date Input</th>
                <th>Payment</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->numberid }}</td>
                    <td>{{ $order->membertext->numberid ?? '-' }}</td>
                    <td>{{ $order->membertext->name ?? '-' }}</td>
                    <td style="text-align: center;">{{ collect($order->products)->sum('quantity') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($order->grandtotal) }}</td>
                    <td>{{ $order->created_at }}</td>
                    <td style="text-transform:uppercase">{{ $order->payment_method }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


    <br><br>
    <table border="0">
        <tbody>
            <tr>
                <td>Number Of Clients</td>
                <td>:</td>
                <td>{{ $numClients }}</td>
            </tr>
            <tr>
                <td>Number Of Pieces</td>
                <td>:</td>
                <td>{{ $numPcs }}</td>
            </tr>
            <tr>
                <td>Total</td>
                <td>:</td>
                <td>Rp {{ number_format($grandTotal, 0, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if (!in_array('out', $ordertype))
        <br><br>
        <table border="0">
            <tbody>
                <tr>
                    <td>Total</td>
                    <td>Cash</td>
                    <td>:</td>
                    <td>Rp {{ isset($totalPayments['cash']) ? number_format($totalPayments['cash']['total']) : 0 }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Card</td>
                    <td>:</td>
                    <td>Rp {{ isset($totalPayments['card']) ? number_format($totalPayments['card']['total']) : 0 }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>PPC</td>
                    <td>:</td>
                    <td>Rp {{ isset($totalPayments['ppc']) ? number_format($totalPayments['ppc']['total']) : 0 }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>OUTSTANDING</td>
                    <td>:</td>
                    <td>Rp {{ isset($totalPayments['outstanding']) ? number_format($totalPayments['outstanding']['total']) : 0 }}</td>
                </tr>
            </tbody>
        </table>

    @else

        <br><br>
        <table border="0">
            <tbody>
                <tr>
                    <td>Total</td>
                    <td>Cash</td>
                    <td>:</td>
                    <td>Rp 0</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Card</td>
                    <td>:</td>
                    <td>Rp 0</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Cash* (Awal)</td>
                    <td>:</td>
                    <td>Rp {{ isset($totalPayments['cash']) ? number_format($totalPayments['cash']['total']) : 0 }}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>Card* (Awal)</td>
                    <td>:</td>
                    <td>Rp {{ isset($totalPayments['card']) ? number_format($totalPayments['card']['total']) : 0 }}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>PPC* (Awal)</td>
                    <td>:</td>
                    <td>Rp {{ isset($totalPayments['ppc']) ? number_format($totalPayments['ppc']['total']) : 0 }}</td>
                </tr>
            </tbody>
        </table>
    @endif



</body>

</html>
