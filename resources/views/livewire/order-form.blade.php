<div>

    @session('message')
    <div class="p-4 bg-green-100">
        {{ $value }}
    </div>
    @endsession

    <form wire:submit.prevent="saveOrder">

    <table class="table table-bordered" id="products_table">
        <thead class="thead-dark">
            <tr>
                <th class="align-middle">Product</th>
                <th class="align-middle text-center">Quantity</th>
                <th class="align-middle text-center">Price</th>
               
                <th class="align-middle text-center">Total</th>
                <th class="align-middle text-center">Action</th>
            </tr>
        </thead>

        <tbody>
{{--            @php--}}
{{--                dd(Cart::instance('order')->content())--}}
{{--            @endphp--}}

            @foreach ($invoiceProducts as $index => $invoiceProduct)
            <tr>
                <td class="align-middle">
                    @if($invoiceProduct['is_saved'])
                        <input type="hidden" name="invoiceProducts[{{$index}}][product_id]" value="{{ $invoiceProduct['product_id'] }}">
                        {{ $invoiceProduct['product_name'] }} - ({{ $invoiceProduct['product_brand_name'] }}) - ({{ $invoiceProduct['product_unit_name'] }})
                    @else
                        <div class="position-relative">
                            <input
                                type="text"
                                wire:model.live="productSearch.{{ $index }}"
                                class="form-control text-center"
                                placeholder="Search product..."
                                autocomplete="off"
                            >

                            @if(!empty($productSuggestions[$index]))
                                <ul
                                    class="list-group position-absolute w-100 bg-white shadow-sm product-suggestions"
                                    style="z-index:1000; border:1px solid #dee2e6;"
                                >
                                    @foreach($productSuggestions[$index] as $product)
                                       <li
                                            class="list-group-item list-group-item-action bg-white d-flex align-items-center gap-2"
                                            wire:click="selectProduct({{ $index }}, {{ $product->id }})"
                                            style="cursor:pointer;"
                                        >
                                            <div class="flex-grow-1 text-truncate">
                                                {{ $product->name }} - ({{ $product->brand?->name ?? 'No Brand' }}) - ({{ $product->unit?->name ?? 'No Unit' }})
                                            </div>

                                            <span class="text-danger fw-bold small">
                                                {{ $product->quantity }}
                                            </span>

                                            <span class="text-white bg-secondary small text-nowrap">
                                                {{ Number::currency($product->selling_price, 'PHP') }}
                                            </span>
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

                <td class="align-middle text-center">
                    @if($invoiceProduct['is_saved'])
                        {{ $invoiceProduct['quantity'] }}

                        <input type="hidden"
                               name="invoiceProducts[{{$index}}][quantity]"
                               value="{{ $invoiceProduct['quantity'] }}"
                        >
                    @else
                        <input
                                    type="number"
                                    wire:model="invoiceProducts.{{$index}}.quantity"
                                    id="invoiceProducts[{{$index}}][quantity]"
                                    class="form-control"
                                    min="1"
                                    max="{{ $invoiceProduct['product_id'] ? $allProducts->find($invoiceProduct['product_id'])->quantity : '' }}"
                                />
                                @error('invoiceProducts.' . $index . '.quantity')
                                    <em class="text-danger">{{ $message }}</em>
                                @enderror
                    @endif
                </td>

                {{--- Unit Price ---}}
                <td class="align-middle text-center">
                    @if($invoiceProduct['is_saved'])
                        {{ $unit_cost = number_format($invoiceProduct['product_price'], 2) }}

                        <input type="hidden"
                               name="invoiceProducts[{{$index}}][unitcost]"
                               value="{{ $unit_cost }}"
                        >
                    @endif
                  
                {{--- Total ---}}
                <td class="align-middle text-center">
                    {{ $product_total = $invoiceProduct['product_price'] * $invoiceProduct['quantity'] + $invoiceProduct['markup_price']  }}

                    <input type="hidden"
                           name="invoiceProducts[{{$index}}][total]"
                           value="{{ $product_total }}"
                    >
                </td>

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
            <tr>
                <td colspan="4"></td>
                <td class="text-center">
                    <button type="button" wire:click="addProduct" class="btn btn-icon btn-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    </button>
                </td>
            </tr>
            <tr>
                <th colspan="4" class="align-middle text-end">
                    Subtotal
                </th>
                <td class="text-center">

                    {{ Number::currency($subtotal, 'PHP') }}
                </td>
            </tr>
            <tr>
                <th colspan="4" class="align-middle text-end">
                    Taxes
                </th>
                <td width="150" class="align-middle text-center">
                    <input wire:model.blur="taxes" type="number" id="taxes" class="form-control w-75 d-inline" min="0" max="100">
                    %

                    @error('taxes')
                    <em class="invalid-feedback">
                        {{ $message }}
                    </em>
                    @enderror
                </td>
            </tr>
            <tr>
                <th colspan="4" class="align-middle text-end">
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
