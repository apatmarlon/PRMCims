@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Create Product') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs')
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <x-alert/>

        <div class="row row-cards">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">
                                    {{ __('Product Image') }}
                                </h3>

                                <img
                                    class="img-account-profile mb-2 bg-black"
                                    src="{{ asset('assets/img/products/default.jpg') }}"
                                    id="image-preview"
                                />

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
                            <div class="card-header">
                                <div>
                                    <h3 class="card-title">
                                        {{ __('Product Create') }}
                                    </h3>
                                </div>

                                <div class="card-actions">
                                    <a href="{{ route('products.index') }}" class="btn-action">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M18 6l-12 12"></path><path d="M6 6l12 12"></path></svg>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row row-cards">
                                    <div class="col-md-12">

                                        <x-input name="name"
                                                 id="name"
                                                 placeholder="Product name"
                                                 value="{{ old('name') }}"
                                        />
                                    </div>
                                         <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="customer_id">
                                                {{ __('Customer') }}
                                                <span class="text-danger">*</span>
                                            </label>

                                            @if ($customers->count() === 1)
                                                <select name="customer_id" id="customer_id"
                                                        class="form-select @error('customer_id') is-invalid @enderror"
                                                        readonly
                                                >
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}" selected>
                                                            {{ $customer->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <select name="customer_id" id="customer_id"
                                                        class="form-select @error('customer_id') is-invalid @enderror"
                                                >
                                                    <option selected="" disabled="">
                                                        Select a customer:
                                                    </option>

                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}" @if(old('customer_id') == $customer->id) selected="selected" @endif>{{ $customer->name }}</option>
                                                    @endforeach
                                                </select>
                                            @endif

                                            @error('customer_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">
                                                Product Category
                                                <span class="text-danger">*</span>
                                            </label>

                                            @if ($categories->count() === 1)
                                                <select name="category_id" id="category_id"
                                                        class="form-select @error('category_id') is-invalid @enderror"
                                                        readonly
                                                >
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" selected>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <select name="category_id" id="category_id"
                                                        class="form-select @error('category_id') is-invalid @enderror"
                                                >
                                                    <option selected="" disabled="">
                                                        Select a category:
                                                    </option>

                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" @if(old('category_id') == $category->id) selected="selected" @endif>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif

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

                                            @if ($units->count() === 1)
                                                <select name="category_id" id="category_id"
                                                        class="form-select @error('category_id') is-invalid @enderror"
                                                        readonly
                                                >
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}" selected>
                                                            {{ $unit->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <select name="unit_id" id="unit_id"
                                                        class="form-select @error('unit_id') is-invalid @enderror"
                                                >
                                                    <option selected="" disabled="">
                                                        Select a unit:
                                                    </option>

                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}" @if(old('unit_id') == $unit->id) selected="selected" @endif>{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            @endif

                                            @error('unit_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <x-input type="number"
                                                label="Buying Price"
                                                name="buying_price"
                                                id="buying_price"
                                                placeholder="0"
                                                value="{{ old('buying_price') }}"
                                        />
                                    </div>

                                    <!-- Hidden Selling Price -->
                                    <input type="hidden"
                                        name="selling_price"
                                        id="selling_price"
                                        value="{{ old('selling_price') }}">
                                   
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
                                                <option value="{{ $brand->id }}" @selected(old('brand_id') == $brand->id)>
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

                                    <div class="col-sm-6 col-md-6">
                                        <x-input type="number"
                                                 label="Quantity"
                                                 name="quantity"
                                                 id="quantity"
                                                 placeholder="0"
                                                 value="{{ old('quantity') }}"
                                        />
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <x-input type="number"
                                                 label="Quantity Alert"
                                                 name="quantity_alert"
                                                 id="quantity_alert"
                                                 placeholder="0"
                                                 value="{{ old('quantity_alert') }}"
                                        />
                                    </div>

                                   <div class="col-sm-6 col-md-6">
                                        <label for="vat" class="form-label">Vat</label>

                                        <select name="vat" id="vat" class="form-control">
                                            <option value="0" {{ old('vat') == '0' ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('vat') == '1' ? 'selected' : '' }}>Yes</option>
                                        </select>
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
                                        <x-input type="date"
                                                 label="Expiry Date"
                                                 name="expiry_date"
                                                 id="expiry_date"
                                                 placeholder="YYYY-MM-DD"
                                                 value="{{ old('expiry_date') }}"
                                        />
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">
                                                {{ __('Notes') }}
                                            </label>

                                            <textarea name="notes"
                                                      id="notes"
                                                      rows="5"
                                                      class="form-control @error('notes') is-invalid @enderror"
                                                      placeholder="Product notes"
                                            ></textarea>

                                            @error('notes')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer text-end">
                                <x-button.save type="submit">
                                    {{ __('Save') }}
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
 <script>
    document.addEventListener('DOMContentLoaded', function () {

        const buyingPrice = document.getElementById('buying_price');
        const sellingPrice = document.getElementById('selling_price');

        buyingPrice.addEventListener('input', function () {
            sellingPrice.value = this.value;
        });

    });
</script>
@endpushonce
