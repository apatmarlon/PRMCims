@extends('layouts.tabler')

@section('content')
<div class="page-body">
<div class="container-xl">

    <h2 class="page-title row md-2 text-white">Product Stock Report</h2>

    {{-- ✅ FILTER FORM --}}
    <div class="card mt-3">
        <div class="card-body">
            <form method="GET" class="row g-3">

                {{-- Product --}}
                <div class="col-md-3 position-relative">
                    <label>Product Name</label>
                    <input type="text" id="product-search" name="product_name" class="form-control"
                        value="{{ $productName ?? request('product_name') }}" placeholder="Search Product">

                    <div id="product-suggestions"
                        class="list-group position-absolute w-100 d-none bg-white border"
                        style="z-index: 1000; max-height: 200px; overflow-y: auto;">
                    </div>
                </div>

                {{-- Quantity --}}
                <div class="col-md-3">
                    <label>Min Qty</label>
                    <input type="number" name="min_qty" class="form-control" value="{{ $minQty }}">
                </div>

                <div class="col-md-3">
                    <label>Max Qty</label>
                    <input type="number" name="max_qty" class="form-control" value="{{ $maxQty }}">
                </div>

                {{-- Supplier --}}
                <div class="col-md-3">
                    <label>Supplier</label>
                    <select name="supplier_id" class="form-control">
                        <option value="">All</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                @selected($supplierId == $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Type --}}
                <div class="col-md-2">
                    <label>Filter Type</label>
                    <select name="filter_type" class="form-control" onchange="this.form.submit()">
                        <option value="monthly" {{ $filterType == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="daily" {{ $filterType == 'daily' ? 'selected' : '' }}>Daily Range</option>
                        <option value="yearly" {{ $filterType == 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>

                {{-- DAILY --}}
                @if($filterType == 'daily')
                <div class="col-md-2">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>

                <div class="col-md-2">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                @endif

                {{-- MONTHLY --}}
                @if($filterType == 'monthly')
                <div class="col-md-2">
                    <label>Select Month</label>
                    <input type="month" name="month" class="form-control"
                        value="{{ $month }}" onchange="this.form.submit()">
                </div>
                @endif

                {{-- YEARLY --}}
                @if($filterType == 'yearly')
                <div class="col-md-2">
                    <label>Select Year</label>
                    <input type="number" name="year" min="2000" max="2100"
                        class="form-control" value="{{ $year }}" onchange="this.form.submit()">
                </div>
                @endif

                <div class="col-md-2 align-self-end">
                    <button class="btn btn-primary w-100">Apply</button>
                </div>

            </form>
        </div>
    </div>

    {{-- ✅ TOTALS --}}
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h4>Total Products</h4>
                    <h2>{{ $totalProducts }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h4>Total Remaining Quantity</h4>
                    <h2>{{ $totalQuantity }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ EXPORT --}}
    <div class="mt-3">
        <a href="{{ route('reports.product-stock.pdf', request()->query()) }}" class="btn btn-danger me-2">
            Download PDF
        </a>

        <a href="{{ route('reports.product-stock.excel', request()->query()) }}" class="btn btn-success">
            Download Excel
        </a>
    </div>

    {{-- ✅ TABLE --}}
    <div class="card mt-3">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Brand</th>
                        <th>Supplier</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="{{ $product->computed_quantity <= $product->quantity_alert ? 'table-danger' : '' }}">
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->brand?->name ?? 'N/A' }}</td>
                        <td>{{ $product->supplier?->name ?? 'N/A' }}</td>
                        <td>{{ $product->computed_quantity }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">No products found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>

{{-- ✅ SEARCH SCRIPT --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    const $input = $("#product-search");
    const $suggestions = $("#product-suggestions");

    $input.on('input', function() {
        const query = $(this).val();

        if(query.length < 2) {
            $suggestions.empty().addClass('d-none');
            return;
        }

        $.ajax({
            url: "{{ route('reports.products.search') }}",
            data: { term: query },
            success: function(data) {
                $suggestions.empty();

                if(data.length === 0) {
                    $suggestions.addClass('d-none');
                    return;
                }

                data.forEach(function(item) {
                    $suggestions.append(
                        `<a href="#" class="list-group-item list-group-item-action" data-value="${item.value}">${item.label}</a>`
                    );
                });

                $suggestions.find('a').on('click', function(e) {
                    e.preventDefault();
                    $input.val($(this).data('value'));
                    $suggestions.empty().addClass('d-none');
                });

                $suggestions.removeClass('d-none');
            }
        });
    });

    $(document).click(function(e) {
        if(!$(e.target).closest('#product-search').length &&
           !$(e.target).closest('#product-suggestions').length) {
            $suggestions.empty().addClass('d-none');
        }
    });
});
</script>

@endsection