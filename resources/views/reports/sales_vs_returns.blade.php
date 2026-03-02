@extends('layouts.tabler')

@section('content')
<div class="page-body">
<div class="container-xl">

    <h2 class="row md-2 text-white">Sales vs. Returns Report</h2>

    {{-- Filter Form --}}
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label>From</label>
            <input type="date" name="from" class="form-control" value="{{ $from ?? '' }}">
        </div>
        <div class="col-md-3">
            <label>To</label>
            <input type="date" name="to" class="form-control" value="{{ $to ?? '' }}">
        </div>
        <div class="col-md-3">
            <label>Customer</label>
            <select name="customer" class="form-control">
                <option value="">All Customers</option>
                @foreach($customers ?? [] as $customer)
                    <option value="{{ $customer->id }}"
                        {{ ($customerId ?? '') == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 align-self-end">
            <button class="btn btn-primary">Filter</button>
        </div>
        <div class="col-md-12 mb-3">
            <a href="{{ route('reports.salesvsreturns.pdf', request()->query()) }}"
            class="btn btn-danger">
                Download PDF
            </a>

            <a href="{{ route('reports.salesvsreturns.excel', request()->query()) }}"
            class="btn btn-success">
                Download Excel
            </a>
        </div>
    </form>

    {{-- Report Table --}}
    <table class="table table-bordered table-striped bg-white">
        <thead class="thead-dark">
            <tr>
                <th>Product</th>
                <th>Brand</th>
                <th>Customer</th>
                <th class="text-center">Sold Qty</th>
                <th class="text-center">Returned Qty</th>
                <th class="text-end">Sales</th>
                <th class="text-end">Returns</th>
                <th class="text-end">Net Sales</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report as $item)
                <tr>
                    <td>{{ $item['product'] }}</td>
                    <td>{{ $item['brand'] }}</td>
                    <td>{{ $item['customer'] ?? '-' }}</td>
                    <td class="text-center">{{ $item['sold_qty'] }}</td>
                    <td class="text-center">{{ $item['returned_qty'] }}</td>
                    <td class="text-end">{{ number_format($item['sales'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['returns'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['sales'] - $item['returns'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
</div>
@endsection
