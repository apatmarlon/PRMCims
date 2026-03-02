@extends('layouts.tabler')

@section('content')
<header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
    <div class="container-xl px-4">
        <div class="page-header-content">
            <div class="row align-items-center justify-content-between pt-3">
                <div class="col-auto mb-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg></div>
                        Purchase Details
                    </h1>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="container-xl px-4">
    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Information Supplier</span>
                    <a href="{{ route('purchases.print', $purchase->id) }}" class="btn btn-info" target="_blank">
                        <i class="fa fa-print"></i> Print
                    </a>
                </div>
                <div class="card-body">
                    <!-- Form Row -->
                    <div class="row gx-3 mb-3">
                        <!-- Form Group (supplier name) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Name</label>
                            <div class="form-control form-control-solid">{{ $purchase->supplier->name }}</div>
                        </div>
                        <!-- Form Group (supplier email) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Email</label>
                            <div class="form-control form-control-solid">{{ $purchase->supplier->email }}</div>
                        </div>
                    </div>
                    <!-- Form Row -->
                    <div class="row gx-3 mb-3">
                        <!-- Form Group (supplier phone number) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Phone</label>
                            <div class="form-control form-control-solid">{{ $purchase->supplier->phone }}</div>
                        </div>
                        <!-- Form Group (order date) -->
                        <div class="col-md-6">
                            <label class="small mb-1">Order Date</label>
                            <div class="form-control form-control-solid">{{ $purchase->date ? $purchase->date->format('d-m-Y') : 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="row gx-3 mb-3">
                        <div class="col-md-6">
                            <label class="small mb-1">No Purchase</label>
                            <div class="form-control form-control-solid">{{ $purchase->purchase_no }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small mb-1">Address</label>
                            <div class="form-control form-control-solid">{{ $purchase->supplier->address }}</div>
                        </div>
                    </div>

                    <div class="row gx-3 mb-3">
                        <div class="col-md-6">
                            <label class="small mb-1">Created By</label>
                            <div class="form-control form-control-solid">{{ $purchase->createdBy ? $purchase->createdBy->name : '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small mb-1">Updated By</label>
                            <div class="form-control form-control-solid">{{ $purchase->updatedBy ? $purchase->updatedBy->name : '-' }}</div>
                        </div>
                    </div>

                
                    <div class="mb-3">
                        <label  class="small mb-1">Total Amount</label>
                        <div class="form-control form-control-solid">{{ Number::currency($purchase->total_amount, 'PHP')}}</div>
                    </div>

                    @if ($purchase->status === \App\Enums\PurchaseStatus::PENDING)
                    <form action="{{ route('purchases.update', $purchase) }}" method="POST">
                        @csrf
                        @method('put')
                        <input type="hidden" name="id" value="{{ $purchase->id }}">
                        <!-- Submit button -->
                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this purchase?')">Approve Purchase</button>
                        <a class="btn btn-primary"  href="{{ url()->previous() }}">Back</a>
                    </form>
                    @else
                    <a class="btn btn-primary"  href="{{ url()->previous() }}">Back</a>
                    @endif
                    
                </div>
            </div>
        </div>

        <div class="col-xl-12">
            <div class="card mb-4 mb-xl-0">
                <div class="card-header">
                    List Product
                </div>

                <div class="card-body">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">No.</th>
                                       
                                        <th scope="col">Product Name</th>
                                        <th scope="col">Product Code</th>
                                        <th scope="col">Current Stock</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Discount Percent</th>
                                        <th scope="col">Discount Amount</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                    <tr>
                                        <td scope="row">{{ $loop->iteration  }}</td>
                                        <td scope="row">{{ $product->product->name }}</td>
                                        <td scope="row">{{ $product->product->code }}</td>
                                        <td scope="row"><span class="btn btn-warning">{{ $product->product->quantity }}</span></td>
                                        <td scope="row"><span class="btn btn-success">{{ $product->quantity }}</span></td>
                                          <td class="text-right amount">
                                            @if($product->is_freebie)
                                                <span>FREE</span>
                                            @else
                                                {{ Number::currency($product->unitcost, 'PHP') }}
                                            @endif
                                        </td>
                                        <td scope="row">{{ $product->discount_percentage }}%</td>
                                        <td scope="row">{{ $product->discount_amount }}</td>
                                        
                                        <td class="text-right amount">
                                            @if($product->is_freebie)
                                                <span class="btn btn-primary">FREE (₱0.00)</span>
                                            @else
                                                <span class="btn btn-primary">
                                                    {{ Number::currency($product->total, 'PHP') }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mt-4 mb-5 text-center">
    <a href="{{ route('purchases.print', $purchase->id) }}" class="btn btn-info" target="_blank">
        <i class="fa fa-print"></i> Print
    </a>
</div>

@endsection
