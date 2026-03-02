@extends('layouts.tabler')

@section('content')
<div class="page-body">
<div class="container-xl">

<h2 class="row md-2 text-white">Product Sales Report</h2>

<form method="GET" class="row g-3 mb-4">

    
    <div class="col-md-3 position-relative">
        <label>Product Name</label>
        <input type="text" id="product-search" name="product_name"
            class="form-control"
            value="{{ request('product_name') }}"
            placeholder="Search Product">

        <div id="product-suggestions"
            class="list-group position-absolute w-100 d-none bg-white border"
            style="z-index:1000; max-height:200px; overflow-y:auto;">
        </div>
    </div>


    <div class="col-md-2">
        <label>Filter</label>
        <select name="type" class="form-select">
            <option value="">Custom</option>
            <option value="daily">Daily</option>
            <option value="monthly">Monthly</option>
            <option value="yearly">Yearly</option>
        </select>
    </div>

    <div class="col-md-2">
        <label>From</label>
        <input type="date" name="from" class="form-control">
    </div>

    <div class="col-md-2">
        <label>To</label>
        <input type="date" name="to" class="form-control">
    </div>

    <div class="col-md-3 align-self-end">
        <button class="btn btn-primary w-100">Apply</button>
    </div>

</form>

{{-- Totals --}}
<div class="row mb-3">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4>Total Quantity</h4>
                <h2>{{ $totalQty }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4>Total Net Cost</h4>
                <h2>₱{{ number_format($totalNetCost, 2) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4>Gross Profit</h4>
                <h2>₱{{ number_format($totalGrossProfit, 2) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h4>Total Sales</h4>
                <h2>₱{{ number_format($totalSales,2) }}</h2>
            </div>
        </div>
    </div>
</div>

{{-- Export --}}
<div class="mb-3">
    <a href="{{ route('reports.product-orders.pdf', request()->query()) }}"
       class="btn btn-danger me-2">Download PDF</a>

    <a href="{{ route('reports.product-orders.excel', request()->query()) }}"
       class="btn btn-success">Download Excel</a>
</div>

<div class="card">
<div class="table-responsive">
<table class="table table-bordered">
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
@forelse($records as $row)
@php
    $unitCost     = $row->product?->buying_price ?? 0;
    $sellingPrice = $row->unitcost ?? 0;
    $qty          = $row->quantity ?? 0;

    $netCost      = $unitCost * $qty;       // NEW
    $grossProfit  = ($sellingPrice - $unitCost) * $qty;
@endphp
<tr>
    <td>{{ $row->order_date }}</td>
    <td>
        {{ $row->product?->name ?? 'Deleted Product' }}
        - ({{ $row->product?->brand?->name ?? 'N/A' }})
    </td>
    <td>{{ $qty }}</td>
    <td>₱{{ number_format($unitCost, 2) }}</td>
    <td>₱{{ number_format($sellingPrice, 2) }}</td>
    <td>₱{{ number_format($netCost, 2) }}</td> <!-- NEW -->
    <td>₱{{ number_format($grossProfit, 2) }}</td>
    <td>₱{{ number_format($row->total, 2) }}</td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center">No records found</td>
</tr>
@endforelse
</tbody>

</table>
</div>
</div>

</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {

    const $input = $('#product-search');
    const $list  = $('#product-suggestions');

    $input.on('input', function () {
        let term = $(this).val();

        if (term.length < 2) {
            $list.empty().addClass('d-none');
            return;
        }

        $.ajax({
            url: "{{ route('reports.products.search') }}",
            data: { term: term },
            success: function (data) {

                $list.empty();

                if (!data.length) {
                    $list.addClass('d-none');
                    return;
                }

                data.forEach(item => {
                    $list.append(`
                        <a href="#" class="list-group-item list-group-item-action"
                           data-value="${item.value}">
                            ${item.label}
                        </a>
                    `);
                });

                $list.removeClass('d-none');

                // click suggestion
                $list.find('a').on('click', function (e) {
                    e.preventDefault();
                    $input.val($(this).data('value'));
                    $list.empty().addClass('d-none');
                });
            }
        });
    });

    // click outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#product-search, #product-suggestions').length) {
            $list.empty().addClass('d-none');
        }
    });

});
</script>

@endsection
