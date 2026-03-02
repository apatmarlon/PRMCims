<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gross Profit Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2, h4 { margin: 0; padding: 0; }
    </style>
</head>
<body>

<h2>Gross Profit Report</h2>
<p>
    @if($filterType == 'daily')
        Date Range: {{ $startDate }} - {{ $endDate }}
    @elseif($filterType == 'monthly')
        Month: {{ $month }}
    @elseif($filterType == 'yearly')
        Year: {{ $year }}
    @endif
</p>

<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity Sold</th>
            <th>Net Cost</th>  {{-- NEW --}}
            <th>Total Sales</th>
            <th>Gross Profit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($grossProfitReport as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->quantity_sold }}</td>
            <td>{{ number_format($item->net_cost, 2) }}</td>
            <td>{{ number_format($item->total_sales, 2) }}</td>
            <td>{{ number_format($item->gross_profit, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <th></th>
            <th>{{ number_format($totalNetCost, 2) }}</th>
            <th>{{ number_format($totalSales, 2) }}</th>
            <th>{{ number_format($totalGrossProfit, 2) }}</th>
        </tr>
    </tfoot>
</table>

</body>
</html>
