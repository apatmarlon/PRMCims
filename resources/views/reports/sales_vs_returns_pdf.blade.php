<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sales vs Returns Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        /* HEADER */
        .invoice-top {
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-info p {
            margin: 2px 0;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
        }

        .date-range {
            text-align: center;
            margin-top: 5px;
            font-size: 11px;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        tfoot th {
            background-color: #e0e0e0;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>

<!-- COMPANY HEADER -->
<div class="invoice-top">
    <div class="company-info">
        <div class="company-name">DYC Car Parts Trading & Rental Services</div>
        <p><strong>Phone:</strong> +639755641064</p>
        <p>Abaga, Lala, Lanao del Norte, 9211, Philippines</p>
    </div>
</div>

<h2>Sales vs Returns Report</h2>

@if(!empty($from) || !empty($to))
    <div class="date-range">
        Period:
        {{ $from ? \Carbon\Carbon::parse($from)->format('M d, Y') : 'Start' }}
        -
        {{ $to ? \Carbon\Carbon::parse($to)->format('M d, Y') : 'End' }}
    </div>
@endif

<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Brand</th>
            <th>Customer</th>
            <th>Sold Qty</th>
            <th>Returned Qty</th>
            <th>Sales</th>
            <th>Returns</th>
            <th>Net Sales</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalSales = 0;
            $totalReturns = 0;
        @endphp

        @foreach($report as $item)
            @php
                $net = $item['sales'] - $item['returns'];
                $totalSales += $item['sales'];
                $totalReturns += $item['returns'];
            @endphp
            <tr>
                <td>{{ $item['product'] }}</td>
                <td>{{ $item['brand'] }}</td>
                <td>{{ $item['customer'] ?? '-' }}</td>
                <td>{{ $item['sold_qty'] }}</td>
                <td>{{ $item['returned_qty'] }}</td>
                <td class="text-right">{{ number_format($item['sales'], 2) }}</td>
                <td class="text-right">{{ number_format($item['returns'], 2) }}</td>
                <td class="text-right">{{ number_format($net, 2) }}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        @php
            $totalNet = $totalSales - $totalReturns;
        @endphp
        <tr>
            <th colspan="5">Totals</th>
            <th class="text-right">{{ number_format($totalSales, 2) }}</th>
            <th class="text-right">{{ number_format($totalReturns, 2) }}</th>
            <th class="text-right">{{ number_format($totalNet, 2) }}</th>
        </tr>
    </tfoot>
</table>

</body>
</html>
