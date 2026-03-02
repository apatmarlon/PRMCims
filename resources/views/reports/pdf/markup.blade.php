<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Markup Report</title>
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

<h2>Markup Report</h2>

<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity Sold</th>
            <th>Sales Without Markup</th>
            <th>Total Markup</th>
            <th>Sales With Markup</th>
        </tr>
    </thead>
    <tbody>
        @foreach($markupReport as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->quantity_sold }}</td>
            <td>{{ number_format($item->sales_without_markup, 2) }}</td>
            <td>{{ number_format($item->total_markup, 2) }}</td>
            <td>{{ number_format($item->sales_with_markup, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">Totals</th>
            <th>{{ number_format($totalSalesWithoutMarkup, 2) }}</th>
            <th>{{ number_format($totalMarkup, 2) }}</th>
            <th>{{ number_format($totalSalesWithMarkup, 2) }}</th>
        </tr>
    </tfoot>
</table>

</body>
</html>
