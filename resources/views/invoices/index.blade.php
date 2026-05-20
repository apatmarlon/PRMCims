<!DOCTYPE html>
<html lang="en">
<head>
    <title>Inventory</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">

    <!-- External CSS libraries -->
    <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/css/bootstrap.min.css') }}">

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Custom Stylesheet -->
    <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/css/style.css') }}">
</head>
<body>
    <div class="invoice-16 invoice-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- BEGIN: Invoice Details -->
                    <div class="invoice-inner-9" id="invoice_wrapper">
                        <div class="invoice-top">
                            <div class="row">
                                <div class="col-lg-6 col-sm-6">
                                   
                                   
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="invoice">
                                        <h1>Invoice # <span>123456</span></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-info">
                            <div class="row">
                                <div class="col-sm-6 mb-50">
                                    <div class="invoice-number">
                                        <h4 class="inv-title-1">Invoice date:</h4>
                                        <p class="invo-addr-1">
                                            {{ \Carbon\Carbon::parse($order_date)->format('F d, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 mb-50">
                                    <h4 class="inv-title-1">Customer</h4>
                                    <p class="inv-from-1">{{ $customer->desc }}</p>
                                    <p class="inv-from-1">{{ $customer->phone }}</p>
                                    <p class="inv-from-1">{{ $customer->email }}</p>
                                    <p class="inv-from-2">{{ $customer->address }}</p>
                                </div>
                                <div class="col-sm-6 text-end mb-50">
                                    <p class="inv-from-1">Provincial Government of Lanao del Norte</p>
                                    <p class="inv-from-1">PRMC Warehouse</p>
                                    <p class="inv-from-2">Pigcarangan, Tubod, Lanao del Norte</p>
                                </div>
                            </div>
                        </div>
                        @php
                            $totalMarkup = 0;

                            foreach ($carts as $item) {
                                $totalMarkup += (float) ($item->options->markup ?? 0);
                            }
                        @endphp
                        <div class="order-summary">
                            <div class="table-outer">
                                <table class="default-table invoice-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Item</th>
                                            <th class="text-center">Price</th>
                                            <th class="text-center">Quantity</th>
                                            <!-- <th class="text-center">Markup</th> -->
                                            <th class="text-center">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($carts as $item)
                                       <tr>
                                            <td class="text-center">{{ $item->name }}</td>
                                            <td class="text-center"> {{ Number::currency(
                                                    ($item->price) + ($item->options->markup ?? 0),
                                                    'PHP'
                                                ) }}</td>
                                            <td class="text-center">{{ $item->qty }}</td>
                                            <!-- <td class="text-center">
                                                {{ Number::currency($item->options->markup ?? 0, 'PHP') }}
                                            </td> -->
                                            <td class="text-center">
                                                {{ Number::currency(
                                                    ($item->price * $item->qty) + ($item->options->markup ?? 0),
                                                    'PHP'
                                                ) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Subtotal</strong></td>
                                            <td class="text-center">
                                                {{ Number::currency((float) Cart::subtotal() + $totalMarkup, 'PHP') }}
                                            </td>
                                        </tr>
                                        <!-- <tr>
                                            <td colspan="4" class="text-end"><strong>Markup</strong></td>
                                            <td class="text-center">
                                                {{ Number::currency($totalMarkup, 'PHP') }}
                                            </td>
                                        </tr> -->
                                       
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total</strong></td>
                                            <td class="text-center">
                                                {{ Number::currency((float) Cart::total() + $totalMarkup, 'PHP') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- <div class="invoice-informeshon-footer">
                            <ul>
                                <li><a href="#">www.website.com</a></li>
                                <li><a href="mailto:sales@hotelempire.com">info@example.com</a></li>
                                <li><a href="tel:+088-01737-133959">+62 123 123 123</a></li>
                            </ul>
                        </div> --}}
                    </div>
                    <!-- END: Invoice Details -->

                    <!-- BEGIN: Invoice Button -->
                    <div class="invoice-btn-section clearfix d-print-none">
                        <a class="btn btn-lg btn-primary" href="{{ route('orders.index') }}">
                            {{ __('Back') }}
                        </a>

                        <form action="{{ route('orders.store') }}" method="POST" class="d-inline">
                            @csrf

                            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                            <input type="hidden" name="note" value="{{ $note }}">
                            <input type="hidden" name="order_date" value="{{ request('order_date') }}">

                            <!-- AUTO PAYMENT -->
                            <input type="hidden" name="payment_type" value="HandCash">
                            <input type="hidden" name="pay" value="{{ (float) Cart::total() + $totalMarkup }}">

                            <button class="btn btn-lg btn-download" type="submit">
                                {{ __('Submit') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

</body>
</html>
