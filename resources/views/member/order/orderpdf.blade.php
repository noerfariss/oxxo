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
    <h3>Report Data Order</h3>
    <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>

    <table border="1">
        <thead>
            <tr>
                <th>No. Bill</th>
                <th>ID</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Grandtotal</th>
                <th>Date Input</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->numberid }}</td>
                    <td>{{ $order->membertext->numberid ?? '-' }}</td>
                    <td>{{ $order->membertext->name ?? '-' }}</td>
                    <td>{{ collect($order->products)->sum('quantity') }}</td>
                    <td>Rp {{ number_format($order->grandtotal) }}</td>
                    <td>{{ $order->created_at }}</td>
                    <td>{{ $order->statuslabel }}</td>
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
                <td>Rp {{ number_format($grandTotal,0,2) }}</td>
            </tr>
        </tbody>
    </table>

</body>

</html>
