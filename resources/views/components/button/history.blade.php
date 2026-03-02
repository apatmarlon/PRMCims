@props(['href' => null])

@if($href)
    <a href="{{ $href }}" {{ $attributes->class(['btn btn-primary']) }}>
        <x-icon.history/>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->class(['btn btn-primary']) }}>
        <x-icon.history/>
        {{ $slot }}
    </button>
@endif