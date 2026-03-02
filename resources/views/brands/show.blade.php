@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Brand Details') }}</h3>
                <div class="card-actions">
                    <x-action.close route="{{ route('brands.index') }}" />
                </div>
            </div>

            <div class="card-body">
                <p><strong>{{ __('Brand Name') }}:</strong> {{ $brand->name }}</p>
                <p><strong>{{ __('Note') }}:</strong> {{ $brand->note ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
