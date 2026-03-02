<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Stock Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 5px; text-align: left; }
    </style>
</head>
<body>
    <div class="row">
        <div class="col-lg-6 col-sm-6">
            <div class="logo">
                <h2 style="margin:0;color:green;">DYC Car Parts Trading & Rental Services</h2>
            </div>
        </div>
    </div>
    <h3>Product Stock Report</h3>
    <p>Date: {{ date('Y-m-d') }}</p>
    @if($supplier)
        <p>Supplier: {{ $supplier->name }}</p>
    @endif
   

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Brand</th>
                <th>Supplier</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->brand?->name ?? 'N/A' }}</td>
                <td>{{ $product->supplier?->name ?? 'N/A' }}</td>
                <td>{{ $product->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p>Total Products: {{ $count }}</p>
    <p>Total Quantity: {{ $totalQty }}</p>
</body>
</html>
