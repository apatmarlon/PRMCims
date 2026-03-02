<div>
    <table class="table table-bordered" id="products_table">
        <thead class="thead-dark">
           <tr>
                <th class="align-middle">Product</th>
                <th class="align-middle text-center">Quantity</th>
                <th class="align-middle text-center">Price</th>
                <th class="align-middle text-center">Disc. (%)</th>
                <th class="align-middle text-center">Disc. Amt</th>
                <th class="align-middle text-center">Freebie</th>
                <th class="align-middle text-center">Total</th>
                <th class="align-middle text-center">Action</th>
            </tr>
        </thead>

        <tbody>

        <!-- ========================= PRODUCT ROWS ========================= -->
        @foreach ($invoiceProducts as $index => $invoiceProduct)
        <tr>

            <!-- Product -->
            <td class="align-middle">
                @if($invoiceProduct['is_saved'])
                    <input type="hidden" name="invoiceProducts[{{$index}}][product_id]" value="{{ $invoiceProduct['product_id'] }}">
                    {{ $invoiceProduct['product_name'] }} - ({{ $invoiceProduct['product_brand_name'] }})
                @else
                  <div class="position-relative">
                        <input type="text" wire:model.live="productSearch.{{ $index }}" 
                            class="form-control text-center" 
                            placeholder="Search product..." autocomplete="off">

                        @if(!empty($productSuggestions[$index]))
                            <ul class="list-group position-absolute product-suggestions">
                                @foreach($productSuggestions[$index] as $product)
                                    <li class="list-group-item list-group-item-action bg-white position-relative"
                                        wire:click="selectProduct({{ $index }}, {{ $product->id }})"
                                        style="cursor:pointer;">

                                        <!-- Main suggestion text -->
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="text-truncate" style="max-width: 200px;">
                                                {{ $product->name }} - ({{ $product->brand?->name ?? 'No Brand' }})
                                            </div>
                                            <div class="d-flex gap-2">
                                                <span class="text-danger fw-bold small"> {{ $product->quantity }} </span>
                                                <span class="text-white bg-secondary small text-nowrap"> {{ Number::currency($product->buying_price, 'PHP') }} </span>
                                            </div>
                                        </div>

                                        <!-- Hover details card -->
                                        <div class="product-hover-details">
                                            <strong>Name:</strong> {{ $product->name }} <br>
                                            <strong>Brand:</strong> {{ $product->brand?->name ?? 'No Brand' }} <br>
                                            <strong>Qty:</strong> {{ $product->quantity }} <br>
                                            <strong>Supplier:</strong> {{ $product->supplier?->name ?? 'N/A' }} <br>
                                            <strong>Price:</strong> {{ Number::currency($product->buying_price, 'PHP') }} <br>
                                        </div>

                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    @error('invoiceProducts.' . $index)
                        <em class="text-danger">{{ $message }}</em>
                    @enderror
                @endif
            </td>


            <!-- Quantity -->
            <td class="align-middle text-center">
                @if($invoiceProduct['is_saved'])
                    {{ $invoiceProduct['quantity'] }}

                    <input type="hidden"
                           name="invoiceProducts[{{$index}}][quantity]"
                           value="{{ $invoiceProduct['quantity'] }}">

                @else
                    <input type="number"
                           wire:model.live="invoiceProducts.{{$index}}.quantity"
                           class="form-control" />
                @endif
            </td>

            <!-- Price -->
           <td class="align-middle text-center">
                @if($invoiceProduct['is_saved'])
                    {{ number_format($invoiceProduct['product_price'], 2) }}

                    <input type="hidden"
                        name="invoiceProducts[{{$index}}][unitcost]"
                        value="{{ $invoiceProduct['product_price'] }}">
                @else
                    <input type="number"
                        step="0.01"
                        min="0"
                        wire:model.live="invoiceProducts.{{$index}}.product_price"
                        class="form-control text-center"
                        @disabled($invoiceProduct['is_freebie'])>
                @endif
            </td>

            <!-- Discount % -->
            <td class="align-middle text-center">
                @if($invoiceProduct['is_saved'])
                    {{ $invoiceProduct['discount_percentage'] ?? 0 }}%

                    <input type="hidden"
                        name="invoiceProducts[{{$index}}][discount_percentage]"
                        value="{{ $invoiceProduct['discount_percentage'] ?? 0 }}">

                @else
                    <input type="number"
                    min="0"
                    wire:model.live="invoiceProducts.{{$index}}.discount_percentage"
                    class="form-control text-center"
                    placeholder="0"
                    @disabled($invoiceProduct['is_freebie'])>
                @endif
            </td>

            <!-- Discount Amount -->
            @php
                $isFreebie = $invoiceProduct['is_freebie'] ?? false;
                $price   = $isFreebie ? 0 : $invoiceProduct['product_price'];
                $qty     = $invoiceProduct['quantity'];
                $percent = $isFreebie ? 0 : ($invoiceProduct['discount_percentage'] ?? 0);

                $discount_amount = 0;
                $row_total = $isFreebie ? 0 : (($price * $qty) - ((($price * $qty) * $percent) / 100));
            @endphp

            <td class="align-middle text-center">
                {{ Number::currency($discount_amount, 'PHP') }}

                <input type="hidden"
                    name="invoiceProducts[{{$index}}][discount_amount]"
                    value="{{ $discount_amount }}">
            </td>
            <td class="align-middle text-center">
                @if($invoiceProduct['is_saved'])
                    @if($invoiceProduct['is_freebie'])
                        <span class="badge bg-success">FREE</span>
                    @else
                        —
                    @endif

                    <input type="hidden"
                        name="invoiceProducts[{{$index}}][is_freebie]"
                        value="{{ $invoiceProduct['is_freebie'] ? 1 : 0 }}">
                @else
                    <input type="checkbox"
                        wire:model.live="invoiceProducts.{{$index}}.is_freebie">
                @endif
            </td>

            <!-- Row Total -->
            <td class="align-middle text-center">
                {{ $invoiceProduct['is_freebie'] ? '-' : Number::currency($row_total, 'PHP') }}

                <input type="hidden"
                    name="invoiceProducts[{{$index}}][total]"
                    value="{{ $row_total }}">
            </td>

            <!-- Actions -->
            <td class="align-middle text-center">
                @if($invoiceProduct['is_saved'])
                    <button type="button" wire:click="editProduct({{$index}})" class="btn btn-icon btn-outline-warning">
                       <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                       </button>
                @elseif($invoiceProduct['product_id'])
                    <button type="button" wire:click="saveProduct({{$index}})" class="btn btn-icon btn-outline-success mr-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
                    </button>
                @endif

               
            </td>

        </tr>
        @endforeach

        <!-- Add product row -->
        <tr>
            <td colspan="7"></td>
            <td class="text-center">
                <button type="button" wire:click="addProduct" class="btn btn-icon btn-success">
                    ＋
                </button>
            </td>
        </tr>

        <!-- ========================= SUBTOTAL ========================= -->
        <tr>
            <th colspan="7" class="align-middle text-end">
                Subtotal
            </th>
            <td class="text-center">
                {{ Number::currency($subtotal, 'PHP') }}
            </td>
        </tr>

        <!-- ========================= TAXES ========================= -->
        <tr>
            <th colspan="7" class="align-middle text-end">
                Taxes
            </th>
            <td class="text-center">
                <input wire:model.blur="taxes" type="number"
                       class="form-control w-75 d-inline"
                       min="0" max="100">

                %

                @error('taxes')
                    <em class="invalid-feedback">{{ $message }}</em>
                @enderror
            </td>
        </tr>

        <!-- ========================= GRAND TOTAL ========================= -->
        <tr>
            <th colspan="7" class="align-middle text-end">
                Total
            </th>
            <td class="text-center">
                {{ Number::currency($total, 'PHP') }}

                <input type="hidden" name="total_amount" value="{{ $total }}">
            </td>
        </tr>

        </tbody>
    </table>
</div>
