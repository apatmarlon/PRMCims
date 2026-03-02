@extends('layouts.tabler')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">
                            {{ __('Order Details') }}
                        </h3>
                    </div>

                    <div class="card-actions btn-actions">
                        <div class="dropdown">
                            <a href="#" class="btn-action dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><!-- Download SVG icon from http://tabler-icons.io/i/dots-vertical -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path><path d="M12 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path><path d="M12 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path></svg>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" style="">
                                @if ($order->order_status === \App\Enums\OrderStatus::PENDING)
                                    <form action="{{ route('orders.update', $order) }}" method="POST">
                                        @csrf
                                        @method('put')

                                        <button type="submit" class="dropdown-item text-success"
                                                onclick="return confirm('Are you sure you want to approve this order?')"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>

                                            {{ __('Approve Order') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <x-action.close route="{{ route('orders.index') }}"/>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row row-cards mb-3">
                        <div class="col">
                            <label for="order_date" class="form-label required">
                                {{ __('Order Date') }}
                            </label>
                            <input type="text"
                                   id="order_date"
                                   class="form-control"
                                   value="{{ $order->order_date->format('d-m-Y') }}"
                                   disabled
                            >
                        </div>

                        <div class="col">
                            <label for="invoice_no" class="form-label required">
                                {{ __('Invoice No.') }}
                            </label>
                            <input type="text"
                                   id="invoice_no"
                                   class="form-control"
                                   value="{{ $order->invoice_no }}"
                                   disabled
                            >
                        </div>

                        <div class="col">
                            <label for="customer" class="form-label required">
                                {{ __('Customer') }}
                            </label>
                            <input type="text"
                                   id="customer"
                                   class="form-control"
                                   value="{{ $order->customer->name }}"
                                   disabled
                            >
                        </div>

                        <div class="col">
                            <label for="payment_type" class="form-label required">
                                {{ __('Payment Type') }}
                            </label>

                            <input type="text" id="payment_type" class="form-control" value="{{ $order->payment_type }}" disabled>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col" class="align-middle text-center">No.</th>
                                <th scope="col" class="align-middle text-center">Product Name</th>
                                <th scope="col" class="align-middle text-center">Brand</th>
                                <th scope="col" class="align-middle text-center">Quantity</th>
                                <th scope="col" class="align-middle text-center">Price</th>
                                <th scope="col" class="align-middle text-center">Total</th>
                            </tr>
                            </thead>
                            @php
                                $subTotal = 0;
                                $totalMarkup = 0;

                                foreach ($order->details as $detail) {
                                    $subTotal += $detail->unitcost * $detail->quantity;
                                    $totalMarkup += $detail->markup_price;
                                }
                            @endphp
                            <tbody>
                            @foreach ($order->details as $item)
                                <tr>
                                    <td class="align-middle text-center">
                                        {{ $loop->iteration  }}
                                    </td>
                                   
                                    <td class="align-middle text-center">
                                        {{ $item->product->name }}
                                    </td>
                                    <td class="align-middle text-center">
                                        {{ $item->product->brand?->name ?? 'No Brand' }}
                                    </td>
                                    <td class="align-middle text-center">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="align-middle text-center">
                                        {{ number_format($item->unitcost, 2) }}
                                    </td>
                                    
                                    <td class="align-middle text-center">
                                        {{ number_format($item->total, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="5" class="text-end">Subtotal</td>
                                <td class="text-center">
                                    {{ number_format($subTotal, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end">Markup</td>
                                <td class="text-center">
                                    {{ number_format($totalMarkup, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end">
                                    Payed amount
                                </td>
                                <td class="text-center">{{ number_format($order->pay, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end">Due</td>
                                <td class="text-center">{{ number_format($order->due, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end">VAT</td>
                                <td class="text-center">{{ number_format($order->vat, 2) }}</td>
                            </tr>
                            <tr>
                            <td colspan="5" class="text-end fw-bold">Total</td>
                                <td class="text-center fw-bold">
                                    {{ number_format($order->total, 2) }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-end">
                    @if ($order->order_status === \App\Enums\OrderStatus::PENDING)
                        <form action="{{ route('orders.update', $order) }}" method="POST">
                            @method('put')
                            @csrf

                        <button type="submit"
                                class="btn btn-success"
                                id="completeOrderBtn">
                            {{ __('Order Completed') }}
                        </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
    <script>
document.getElementById('completeOrderBtn')?.addEventListener('click', function(event) {
    event.preventDefault(); // stop form submit

    Swal.fire({
        title: "Complete this order?",
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, complete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            // submit the form
            event.target.closest('form').submit();
        }
    });

});
</script>
@endsection

