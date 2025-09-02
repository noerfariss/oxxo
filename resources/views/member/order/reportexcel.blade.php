<table width="100%">
     <tr>
        <td colspan="6" style="width: 80px; text-align:center;">
            <img src="{{ public_path('images/logo.png') }}" style="width: 80px;">
        </td>
    </tr>
    <tr>
        <td colspan="6" style="text-align: center;">
            <h2 style="padding: 0; margin:0; font-size:23px;">{{ $title }}</h2>
        </td>
    </tr>
    <tr>
        <td colspan="6" style="text-align: center;">
            <p style="padding:0; margin:0;">Periode: {{ $startDate }} s/d {{ $endDate }}</p>
        </td>
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
<table>
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
</table>

@if (!in_array('out', $ordertype))
    <br><br>
    <table>
        <tr>
            <td>Total</td>
            <td>Cash</td>
            <td>:</td>
            <td>Rp {{ $totalPayments['cash']['total'] ?? 0 }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Card</td>
            <td>:</td>
            <td>Rp {{ $totalPayments['card']['total'] ?? 0 }}</td>
        </tr>
        <tr>
            <td></td>
            <td>PPC</td>
            <td>:</td>
            <td>Rp {{ $totalPayments['ppc']['total'] ?? 0 }}</td>
        </tr>
        <tr>
            <td></td>
            <td>OUTSTANDING</td>
            <td>:</td>
            <td>Rp {{ $totalPayments['outstanding']['total'] ?? 0 }}</td>
        </tr>
    </table>
@else
    <br><br>
    <table>
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
            <td>Rp {{ $totalPayments['cash']['total'] ?? 0 }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Card* (Awal)</td>
            <td>:</td>
            <td>Rp {{ $totalPayments['card']['total'] ?? 0 }}</td>
        </tr>
        <tr>
            <td></td>
            <td>PPC* (Awal)</td>
            <td>:</td>
            <td>Rp {{ $totalPayments['ppc']['total'] ?? 0 }}</td>
        </tr>
    </table>
@endif
