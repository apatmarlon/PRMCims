@extends('layouts.tabler')

@section('content')
<div class="page-body">
<div class="container-xl">

    <h2 class="page-title row md-2 text-white">Product Stock Report</h2>

    {{-- Filter Form --}}
    <form method="GET" class="row mb-4 align-items-end">

        <div class="col-md-3 position-relative">
            <label>Product Name</label>
            <input type="text" id="product-search" name="product_name" class="form-control"
                value="{{ request('product_name') }}" placeholder="Search Product">
            {{-- Suggestions --}}
            <div id="product-suggestions" class="list-group position-absolute w-100 d-none bg-white border"
                style="z-index: 1000; max-height: 200px; overflow-y: auto;"></div>
        </div>

        <div class="col-md-2">
            <label>Min Quantity</label>
            <input type="number" name="min_qty" class="form-control"
                value="{{ $minQty }}" placeholder="0">
        </div>

        <div class="col-md-2">
            <label>Max Quantity</label>
            <input type="number" name="max_qty" class="form-control"
                value="{{ $maxQty }}" placeholder="100">
        </div>

        <div class="col-md-3">
            <label>Supplier</label>
            <select name="supplier_id" class="form-select">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}"
                        @selected($supplierId == $supplier->id)>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100 mt-0">Apply</button>
        </div>

    </form>


    {{-- Totals Cards --}}
    <div class="row mb-3">
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

    {{-- Export Buttons --}}
    <div class="mb-3">
        <a href="{{ route('reports.product-stock.pdf', request()->query()) }}" class="btn btn-danger me-2">
            Download PDF
        </a>
        <a href="{{ route('reports.product-stock.excel', request()->query()) }}" class="btn btn-success">
            Download Excel
        </a>
    </div>

    {{-- Products Table --}}
    <div class="card">
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
                    <tr class="{{ $product->quantity <= $product->quantity_alert ? 'table-danger' : '' }}">
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->brand?->name ?? 'N/A' }}</td>
                        <td>{{ $product->supplier?->name ?? 'N/A' }}</td>
                        <td>{{ $product->quantity }}</td>
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
                    $input.val($(this).data('value')); // fill input with product name only
                    $suggestions.empty().addClass('d-none');
                });
                $suggestions.removeClass('d-none');
            }
        });
    });

    // Close suggestions if clicked outside
    $(document).click(function(e) {
        if(!$(e.target).closest('#product-search').length &&
           !$(e.target).closest('#product-suggestions').length) {
            $suggestions.empty().addClass('d-none');
        }
    });
});
</script>

@endsection
