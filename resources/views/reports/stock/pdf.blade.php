<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .meta {
            width: 100%;
            margin-bottom: 10px;
        }
        .meta td {
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background: #eee;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>ABC Trading Corporation</h2>
    <p>Inventory Management System</p>
    <h3>STOCK REPORT</h3>
</div>

<table class="meta">
    <tr>
        <td><strong>Date Generated:</strong> {{ now()->format('F d, Y') }}</td>
        <td align="right"><strong>Total Items:</strong> {{ $products->count() }}</td>
    </tr>
</table>

<hr>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Code</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Alert Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $p)
        <tr>
            <td>{{ $p->name }}</td>
            <td>{{ $p->code }}</td>
            <td>{{ $p->category->name ?? '-' }}</td>
            <td>{{ $p->quantity }}</td>
            <td>{{ $p->quantity_alert }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
