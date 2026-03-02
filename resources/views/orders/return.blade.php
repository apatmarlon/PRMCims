@extends('layouts.tabler')

@section('content')
<div class="page-body">
<div class="container-xl">

    <h2 class="row md-2 text-white">
        Order Return — #{{ $order->order_no ?? $order->id }}
    </h2>

    <form id="returnForm" method="POST" action="{{ route('orders.return.store', $order) }}">
        @csrf

        <div class="card">
            <div class="card-body">
                @php
                    $allReturned = $order->details->every(fn ($d) => $d->remainingQty() === 0);
                @endphp
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Sold Qty</th>
                            <th class="text-center">Return Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Line Refund</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->details as $detail)
                        <tr>
                            <td>
                                {{ $detail->product->name }}
                                <div class="text-muted small">
                                    {{ $detail->product->brand?->name }}
                                </div>
                            </td>
                            @if($detail->remainingQty() === 0)
                                <td class="text-center">
                                    <span class="btn btn-dark">Fully Returned</span>
                                </td>
                            @else
                                <td class="text-center">
                                    {{ $detail->quantity }}
                                    <div class="text-muted small">
                                        Returned: {{ $detail->returned_quantity }}
                                    </div>
                                    <div class="text-success small">
                                        Remaining: {{ $detail->remainingQty() }}
                                    </div>
                                </td>
                            @endif

                            <td class="text-center" width="130">
                                @if($detail->remainingQty() === 0)
                                    <input type="number" class="form-control text-center" value="0" disabled>
                                @else
                                    <input
                                        type="number"
                                        name="products[{{ $detail->product_id }}]"
                                        min="0"
                                        max="{{ $detail->remainingQty() }}"
                                        value="0"
                                        data-price="{{ (float) $detail->unitcost }}"
                                        class="form-control text-center return-qty"
                                    >
                                @endif
                            </td>

                            <td class="text-end">
                                {{ Number::currency($detail->unitcost, 'PHP') }}
                            </td>

                            <td class="text-end refund-cell">₱0.00</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Return Reason</label>
                        <textarea name="reason" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="col-md-6 text-end">
                        <h3 class="mt-4">
                            Total Refund:
                            <span class="text-danger fw-bold" id="totalRefund">₱0.00</span>
                        </h3>
                    </div>
                </div>

            </div>

            <div class="card-footer text-end">
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Cancel</a>

                <button 
                    type="button" 
                    id="confirmReturnBtn"
                    class="btn btn-danger"
                    @if($allReturned) disabled @endif
                >
                    {{ $allReturned ? 'Fully Returned' : 'Confirm Return' }}
                </button>
            </div>
        </div>
    </form>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // Refund calculation
    document.querySelectorAll('.return-qty').forEach(input => {
        input.addEventListener('input', calculateRefund);
    });
    calculateRefund();

    // SweetAlert confirmation
    const confirmBtn = document.getElementById('confirmReturnBtn');
    const form = document.getElementById('returnForm');

    if (confirmBtn && !confirmBtn.disabled) {
        confirmBtn.addEventListener('click', () => {
            Swal.fire({
                title: 'Confirm Return?',
                text: 'Returned items will be added back to inventory.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, confirm return',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    }
});

function calculateRefund() {
    let total = 0;
    document.querySelectorAll('.return-qty').forEach(input => {
        if (input.disabled) return;
        const qty = Number(input.value || 0);
        const price = Number(input.dataset.price || 0);
        const rowTotal = qty * price;
        input.closest('tr').querySelector('.refund-cell').innerText =
            rowTotal.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
        total += rowTotal;
    });
    document.getElementById('totalRefund').innerText =
        total.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' });
}
</script>
@endsection
