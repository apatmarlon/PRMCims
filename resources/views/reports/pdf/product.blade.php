<!DOCTYPE html>
<html>
<head>
<style>
    body { font-family: sans-serif; font-size: 12px; }
    .invoice-top { border-bottom: 2px solid #000; margin-bottom: 15px; }
    .company-name { font-size: 18px; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #ccc; padding: 6px; }
    th { background: #f2f2f2; }
</style>
</head>

<body>

<div class="invoice-top">
    <div class="company-name">DYC Car Parts Trading & Rental Services</div>
    <p>Phone: +639755641064</p>
    <p>Abaga, Lala, Lanao del Norte, Philippines</p>
</div>

<h2 style="text-align:center">Product Stock Report</h2>

<p>
    <strong>Supplier:</strong> {{ $supplier?->name ?? 'All Suppliers' }} <br>
    <strong>Quantity Range:</strong> {{ $minQty ?? 0 }} – {{ $maxQty ?? '∞' }} <br>
    <strong>Total Products:</strong> {{ $count }} <br>
    <strong>Total Quantity:</strong> {{ $totalQty }}
</p>

<table>
<thead>
<tr>
    <th>Product</th>
    <th>Supplier</th>
    <th>Quantity</th>
    <th>Alert</th>
</tr>
</thead>
<tbody>
@foreach($products as $product)
<tr>
    <td>{{ $product->name }}</td>
    <td>{{ $product->supplier?->name ?? 'N/A' }}</td>
    <td>{{ $product->quantity }}</td>
    <td>{{ $product->quantity_alert }}</td>
</tr>
@endforeach
</tbody>
</table>

</body>
</html>
