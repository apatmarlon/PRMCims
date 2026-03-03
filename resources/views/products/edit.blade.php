@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Edit Product') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs', ['model' => $product])
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('put')

                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">
                                    {{ __('Product Image') }}
                                </h3>

                                <img
                                    class="img-account-profile mb-2 bg-black"
                                    src="{{ $product->product_image ? asset('storage/products/'.$product->product_image) : asset('assets/img/products/default.jpg') }}"
                                    id="image-preview"
                                >

                                <div class="small font-italic text-muted mb-2">
                                    JPG or PNG no larger than 2 MB
                                </div>

                                <input
                                    type="file"
                                    accept="image/*"
                                    id="image"
                                    name="product_image"
                                    class="form-control @error('product_image') is-invalid @enderror"
                                    onchange="previewImage();"
                                >

                                @error('product_image')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">

                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">
                                    {{ __('Product Details') }}
                                </h3>

                                <div class="row row-cards">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">
                                                {{ __('Name') }}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="text"
                                                   id="name"
                                                   name="name"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   placeholder="Product name"
                                                   value="{{ old('name', $product->name) }}"
                                            >

                                            @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">
                                                Product category
                                                <span class="text-danger">*</span>
                                            </label>

                                            <select name="category_id" id="category_id"
                                                    class="form-select @error('category_id') is-invalid @enderror"
                                            >
                                                <option selected="" disabled="">Select a category:</option>
                                                @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" @if(old('category_id', $product->category_id) == $category->id) selected="selected" @endif>{{ $category->name }}</option>
                                                @endforeach
                                            </select>

                                            @error('category_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="unit_id">
                                                {{ __('Unit') }}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <select name="unit_id" id="unit_id"
                                                    class="form-select @error('unit_id') is-invalid @enderror"
                                            >
                                                <option selected="" disabled="">
                                                    Select a unit:
                                                </option>

                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}" @if(old('unit_id', $product->unit_id) == $unit->id) selected="selected" @endif>{{ $unit->name }}</option>
                                                @endforeach
                                            </select>

                                            @error('unit_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="buying_price">
                                                Buying price
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="text"
                                                   id="buying_price"
                                                   name="buying_price"
                                                   class="form-control @error('buying_price') is-invalid @enderror"
                                                   placeholder="0"
                                                   value="{{ old('buying_price', $product->buying_price) }}"
                                            >

                                            @error('buying_price')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="selling_price" class="form-label">
                                                Selling price
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="text"
                                                id="selling_price"
                                                name="selling_price"
                                                class="form-control"
                                                readonly
                                                value="{{ old('selling_price', $product->selling_price) }}">

                                            @error('selling_price')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Margin (%)</label>
                                            <input type="number"
                                                id="margin_percent"
                                                name="margin_percent"
                                                class="form-control"
                                                step="0.01"
                                                value="{{ old('margin_percent', $product->margin_percent ?? '') }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Margin Amount</label>
                                            <input type="number"
                                                id="margin_amount"
                                                name="margin_amount"
                                                class="form-control"
                                                step="0.01"
                                                value="{{ old('margin_amount', $product->margin_amount ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6 mb-3">
                                        <label for="brand_id" class="form-label">
                                            Brand
                                            <span class="text-danger">*</span>
                                        </label>

                                        <select name="brand_id" id="brand_id"
                                                class="form-select @error('brand_id') is-invalid @enderror"
                                        >
                                            <option value="">Select Brand</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id) == $brand->id)>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('brand_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 col-md-6 mb-3">
                                        <label for="supplier_id" class="form-label">
                                            Supplier
                                        </label>

                                        <select name="supplier_id" id="supplier_id"
                                                class="form-select @error('supplier_id') is-invalid @enderror">
                                            <option value="">Select Supplier</option>

                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}"
                                                    @selected(old('supplier_id', $product->supplier_id ?? null) == $supplier->id)>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('supplier_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="quantity" class="form-label">
                                                {{ __('Quantity') }}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="number"
                                                   id="quantity"
                                                   name="quantity"
                                                   class="form-control @error('quantity') is-invalid @enderror"
                                                   min="0"
                                                   value="{{ old('quantity', $product->quantity) }}"
                                                   placeholder="0"
                                            >

                                            @error('quantity')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="quantity_alert" class="form-label">
                                                {{ __('Quantity Alert') }}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="number"
                                                   id="quantity_alert"
                                                   name="quantity_alert"
                                                   class="form-control @error('quantity_alert') is-invalid @enderror"
                                                   min="0"
                                                   placeholder="0"
                                                   value="{{ old('quantity_alert', $product->quantity_alert) }}"
                                            >

                                            @error('quantity_alert')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="tax" class="form-label">
                                                {{ __('Tax') }}
                                            </label>

                                            <input type="number"
                                                   id="tax"
                                                   name="tax"
                                                   class="form-control @error('tax') is-invalid @enderror"
                                                   min="0"
                                                   placeholder="0"
                                                   value="{{ old('tax', $product->tax) }}"
                                            >

                                            @error('tax')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-6" hidden>
                                        <div class="mb-3">
                                            <label class="form-label" for="tax_type">
                                                {{ __('Tax Type') }}
                                            </label>

                                            <select name="tax_type" id="tax_type" 
                                                    class="form-select @error('tax_type') is-invalid @enderror"
                                            >
                                                @foreach(\App\Enums\TaxType::cases() as $taxType)
                                                <option value="{{ $taxType->value }}" @selected(old('tax_type', $product->tax_type) == $taxType->value)>
                                                    {{ $taxType->label() }}
                                                </option>
                                                @endforeach
                                            </select>

                                            @error('tax_type')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="code" class="form-label">
                                                Code
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="text"
                                                id="code"
                                                name="code"
                                                readonly
                                                class="form-control @error('code') is-invalid @enderror"
                                                value="{{ old('code', $product->code) }}"
                                            >

                                            @error('code')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3 mb-0">
                                            <label for="notes" class="form-label">
                                                {{ __('Notes') }}
                                            </label>

                                            <textarea name="notes"
                                                      id="notes"
                                                      rows="5"
                                                      class="form-control @error('notes') is-invalid @enderror"
                                                      placeholder="Product notes"
                                            >{{ old('notes', $product->notes) }}</textarea>

                                            @error('notes')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>`
                                </div>
                            </div>

                            <div class="card-footer text-end">
                                <x-button.save type="submit">
                                    {{ __('Update') }}
                                </x-button.save>

                                <x-button.back route="{{ route('products.index') }}">
                                    {{ __('Back') }}
                                </x-button.back>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@pushonce('page-scripts')
<script src="{{ asset('assets/js/img-preview.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const buyingPrice = document.getElementById('buying_price');
    const marginPercent = document.getElementById('margin_percent');
    const marginAmount = document.getElementById('margin_amount');
    const sellingPrice = document.getElementById('selling_price');

    function calcFromPercent() {
        let bp = parseFloat(buyingPrice.value) || 0;
        let mp = parseFloat(marginPercent.value) || 0;

        let amount = (bp * mp) / 100;
        let sell = bp + amount;

        marginAmount.value = amount.toFixed(2);
        sellingPrice.value = sell.toFixed(2);
    }

    function calcFromAmount() {
        let bp = parseFloat(buyingPrice.value) || 0;
        let ma = parseFloat(marginAmount.value) || 0;

        if (bp === 0) return;

        let percent = (ma / bp) * 100;
        let sell = bp + ma;

        marginPercent.value = percent.toFixed(2);
        sellingPrice.value = sell.toFixed(2);
    }

    function recalcAll() {
        if (marginPercent.value !== '') {
            calcFromPercent();
        } else if (marginAmount.value !== '') {
            calcFromAmount();
        }
    }

    marginPercent.addEventListener('input', calcFromPercent);
    marginAmount.addEventListener('input', calcFromAmount);
    buyingPrice.addEventListener('input', recalcAll);

    // IMPORTANT — RUN ON PAGE LOAD
    recalcAll();
});
</script>
@endpushonce

