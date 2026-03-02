@props([
    'label' => 'Product',
    'id' => 'product_id',
    'name' => 'product_id',
    'placeholder' => 'Select Product',
    'data' => [],
    'index' => null,
])

<div>
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>

    <div
        x-data
        x-init="
            new TomSelect($refs.select, {
                placeholder: '{{ $placeholder }}',
                onChange(value) {
                    @if(!is_null($index))
                        $wire.set('invoiceProducts.{{ $index }}.product_id', value)
                    @endif
                }
            })
        "
    >
        <select x-ref="select" id="{{ $id }}" name="{{ $name }}" class="form-control">
            <option value="">{{ $placeholder }}</option>

            @foreach($data as $item)
                <option value="{{ $item->id }}">
                    {{ $item->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
