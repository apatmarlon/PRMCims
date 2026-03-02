<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase & Sale Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
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

        /* CONTENT */
        h2 {
            text-align: center;
            margin-top: 10px;
        }

        h3 {
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 5px;
            text-align: left;
        }
    </style>
</head>

<body>

<!-- INVOICE-STYLE HEADER -->
<div class="invoice-top">
    <div class="company-info">
        <div class="company-name">
            DYC Car Parts Trading & Rental Services
        </div>
        <p><strong>Phone:</strong> +639755641064</p>
        <p>Abaga, Lala, Lanao del Norte, 9211, Philippines</p>
    </div>
</div>

<h2 class="row md-2 text-white">Purchase & Sale Report</h2>
<p><strong>Period:</strong> {{ $startDate }} - {{ $endDate }}</p>

<h3>Purchases</h3>
<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($purchases as $p)
        <tr>
            <td>{{ $p->product_name }}</td>
            <td>{{ $p->quantity }}</td>
            <td>{{ number_format($p->total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h3>Sales</h3>
<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sales as $s)
        <tr>
            <td>{{ $s->product_name }}</td>
            <td>{{ $s->quantity }}</td>
            <td>{{ number_format($s->total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
