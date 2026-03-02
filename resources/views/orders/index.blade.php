@extends('layouts.tabler')

@section('content')
<div class="page-body">
    @if($orders->isEmpty())
    <x-empty
        title="No sales found"
        message="Try adjusting your search or filter to find what you're looking for."
        button_label="{{ __('Add your first Sale') }}"
        button_route="{{ route('orders.create') }}"
    />
    @else
    <div class="container-xl">
        <x-alert/>

        <livewire:tables.order-table />
    </div>
    @endif
</div>
@endsection
