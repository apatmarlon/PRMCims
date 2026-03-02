@extends('layouts.tabler')

@section('content')
<div class="page-body">
 <div class="container-xl">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Sales Return Details</h3>
            <div class="card-actions">
                <a href="{{ route('orders.return.index') }}" class="btn btn-primary btn-sm">Back to Returns</a>
                <a href="{{ route('orders.return.print', $return) }}"
                    target="_blank"
                    class="btn btn-danger btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="icon"
                            width="18" height="18"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            fill="none"
                            stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M6 9V2h12v7" />
                            <path d="M6 18h12v4H6z" />
                            <path d="M6 14h12" />
                            <path d="M6 11h12" />
                        </svg>
                        Print Return
                    </a>
            </div>
        </div>

        <div class="card-body">
            {{-- Customer Info --}}
            <h5>Customer Information</h5>
            <div class="row mb-3">
                <div class="col-md-4"><strong>Name:</strong> {{ $return->customer->name }}</div>
                <div class="col-md-4"><strong>Phone:</strong> {{ $return->customer->phone }}</div>
                <div class="col-md-4"><strong>Address:</strong> {{ $return->customer->address }}</div>
            </div>

            {{-- Order Info --}}
            <h5>Order Information</h5>
            <div class="row mb-3">
                <div class="col-md-4"><strong>Invoice No:</strong> {{ $return->order->invoice_no }}</div>
                <div class="col-md-4"><strong>Return No:</strong> {{ $return->id }}</div>
                <div class="col-md-4"><strong>Return Date:</strong> {{ $return->created_at->format('d-m-Y') }}</div>
            </div>

            {{-- Returned Products --}}
            <h5>Returned Products</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-vcenter text-nowrap">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($return->order->details as $detail)
                            @php
                                $returnDetail = $return->details->where('product_id', $detail->product_id)->first();
                                if(!$returnDetail) continue;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $detail->product->name }}</td>
                                <td>{{ $returnDetail->quantity }}</td>
                                <td>{{ number_format($returnDetail->unit_price, 2) }}</td>
                                <td>{{ number_format($returnDetail->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No returned products</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Total Refund:</th>
                            <th>{{ number_format($return->total_refund, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Reason --}}
            <h5>Return Reason</h5>
            <p>{{ $return->reason ?? '-' }}</p>

            {{-- Processed By --}}
            <h5>Processed By</h5>
            <p>{{ $return->processed_by }}</p>
        </div>
    </div>
 </div>
</div>
@endsection
