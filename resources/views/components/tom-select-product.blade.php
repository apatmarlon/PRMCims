@pushonce('page-styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
@endpushonce

@props([
    'label' => '',
    'name',
    'id' => null,
    'placeholder' => 'Select...',
    'data' => [],
    'value' => null,
])

@php
    $id = $id ?? $name;
@endphp

<div class="col-md-4">
    <label for="{{ $id }}" class="form-label required">
        {{ $label }}
    </label>

    <select id="{{ $id }}" name="{{ $name }}" autocomplete="off"
        class="form-control form-select @error($name) is-invalid @enderror"
        @if(isset($attributes['wire:model.live'])) wire:model.live="{{ $attributes['wire:model.live'] }}" @endif
    >
        <option value="">{{ $placeholder }}</option>

        @foreach($data as $option)
            <option value="{{ $option['value'] }}" @selected(old($name, $value) == $option['value'])>
                {{ $option['text'] }}
            </option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@pushonce('page-scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('livewire:load', function () {
        const select = new TomSelect("#{{ $id }}", {
            create: false,
            sortField: { field: "text", direction: "asc" },
            highlight: true,
            onChange: function(value) {
                // Update Livewire property when selection changes
                @this.set("{{ str_replace(['[', ']'], ['.',''], $name) }}", value);
            }
        });
    });
</script>
@endpushonce
