@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">

    <h2 class="page-title row md-2 text-white">History of {{ $product->name }} - ({{ $product->brand?->name ?? '' }})  </h2>
        <table class="table table-bordered table-striped bg-white mt-3">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Reference</th>
                    <th>Running Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item['date'])->format('F d Y') }}</td>
                    <td>{{ ucfirst($item['type']) }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ number_format($item['unit_price'], 2) }}</td>
                    <td>{{ number_format($item['total'], 2) }}</td>
                    <td>{{ $item['reference'] }}</td>
                    <td>{{ $item['running_stock'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
