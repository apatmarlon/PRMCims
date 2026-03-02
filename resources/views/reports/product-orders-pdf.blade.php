<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Sales Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
            text-align: center;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .summary {
            font-weight: bold;
            background: #eaeaea;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>

<h2 style="color:green; margin:0;">DYC Car Parts Trading & Rental Services</h2>
<h2>Product Sales Report</h2>

@php
    $totalQty         = 0;
    $totalSales       = 0;
    $totalNetCost     = 0;
    $totalGrossProfit = 0;
@endphp

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Unit Cost</th>
            <th>Selling Price</th>
            <th>Net Cost</th> <!-- NEW -->
            <th>Gross Profit</th>
            <th>Total</th>
        </tr>
    </thead>

    <tbody>
    @foreach($records as $row)
        @php
            $unitCost     = $row->product?->buying_price ?? 0;
            $sellingPrice = $row->unitcost ?? 0;
            $qty          = $row->quantity ?? 0;

            $netCost      = $unitCost * $qty;          // NEW
            $grossProfit  = ($sellingPrice - $unitCost) * $qty;

            $totalQty         += $qty;
            $totalNetCost     += $netCost;             // NEW
            $totalSales       += $row->total;
            $totalGrossProfit += $grossProfit;
        @endphp

        <tr>
            <td>{{ $row->created_at->format('Y-m-d') }}</td>
            <td>
                {{ $row->product?->name ?? 'Deleted Product' }}
                - ({{ $row->product?->brand?->name ?? 'N/A' }})
            </td>
            <td class="text-right">{{ $qty }}</td>
            <td class="text-right">₱{{ number_format($unitCost, 2) }}</td>
            <td class="text-right">₱{{ number_format($sellingPrice, 2) }}</td>
            <td class="text-right">₱{{ number_format($netCost, 2) }}</td> <!-- NEW -->
            <td class="text-right">₱{{ number_format($grossProfit, 2) }}</td>
            <td class="text-right">₱{{ number_format($row->total, 2) }}</td>
        </tr>
    @endforeach

    {{-- SUMMARY --}}
    <tr class="summary">
        <td colspan="2">TOTAL SUMMARY</td>
        <td class="text-right">{{ $totalQty }}</td>
        <td class="text-right">₱{{ number_format($totalNetCost, 2) }}</td>
        <td></td>
        <td class="text-right">₱{{ number_format($totalGrossProfit, 2) }}</td>
        <td class="text-right">₱{{ number_format($totalSales, 2) }}</td>
    </tr>

    </tbody>
</table>

</body>
</html>
