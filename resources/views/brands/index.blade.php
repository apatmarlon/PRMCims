@extends('layouts.tabler')

@section('content')
<div class="page-body">
    @if($brands->isEmpty())
        <x-empty
            title="No brands found"
            message="Try adjusting your search or filter to find what you're looking for."
            button_label="{{ __('Add your first Brand') }}"
            button_route="{{ route('brands.create') }}"
        />
    @else
        <div class="container-xl">
            <x-alert/>

            @livewire('tables.brand-table')
        </div>
    @endif
</div>
@endsection
