<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Supplier & Customer Report</title>
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

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        h3 {
            margin-top: 20px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 4px;
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

<h2>Supplier & Customer Report</h2>

<h3>Customers</h3>
<table>
    <tr>
        <th>Name</th>
        <th>Email</th>
    </tr>
    @foreach ($customers as $c)
    <tr>
        <td>{{ $c->name }}</td>
        <td>{{ $c->email }}</td>
    </tr>
    @endforeach
</table>

<br>

<h3>Suppliers</h3>
<table>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Shop Name</th>
        <th>Type</th>
    </tr>
    @foreach ($suppliers as $s)
    <tr>
        <td>{{ $s->name }}</td>
        <td>{{ $s->email }}</td>
        <td>{{ $s->shopname }}</td>
        <td>{{ $s->type }}</td>
    </tr>
    @endforeach
</table>

</body>
</html>
