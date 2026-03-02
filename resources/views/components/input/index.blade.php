@props([
    'label' => null,
    'type' => 'text',
    'name',
    'id' => null,
    'placeholder' => null,
    'autocomplete' => 'off',
    'readonly' => false,
    'disabled' => false,
    'required' => false,
    'value' => null,
])

@php
    $id = $id ?? $name;
    $label = $label ?? ucfirst($name);
    $value = $value ?? old($name);
@endphp

<div class="mb-3">
    <label for="{{ $id }}" class="form-label @error($name) text-danger @enderror {{ $required ? 'required' : '' }}">
        {{ __($label) }}
    </label>

    <input
        {{ $attributes->merge([
            'type' => $type,
            'name' => $name,
            'id' => $id,
            'class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : ''),
            'placeholder' => $placeholder,
            'autocomplete' => $autocomplete,
            'value' => $value,
        ]) }}
        @if($readonly) readonly @endif
        @if($disabled) disabled @endif
        @if($required) required @endif
    >

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
