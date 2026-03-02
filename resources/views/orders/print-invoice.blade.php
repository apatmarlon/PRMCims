<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
            Inventory System
        </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <!-- External CSS libraries -->
        <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/css/bootstrap.min.css') }}">
        <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/fonts/font-awesome/css/font-awesome.min.css') }}">
        <!-- Google fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <!-- Custom Stylesheet -->
        <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/css/style.css') }}">
<style>
@media print {

    /* A4 Portrait */
    @page {
        size: A4 portrait;
        margin: 10mm;
    }

    html, body {
        margin: 0 !important;
        padding: 0 !important;
        height: auto !important;
        min-height: auto !important;
        background: #fff !important;

        /* 🔽 THIS MAKES EVERYTHING SMALL */
        font-size: 8.5px;
        line-height: 1.15;
    }

    body {
        display: block !important;
    }

    /* TOP FLOW – NO CENTERING */
    .invoice-16,
    .invoice-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        transform: none !important;
    }

    .container,
    .invoice-inner-9 {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    /* SMALLER HEADINGS */
    h1 { font-size: 13px; margin: 2px 0; }
    h2 { font-size: 12px; margin: 2px 0; }
    h3 { font-size: 11px; margin: 2px 0; }
    h4 { font-size: 10px; margin: 2px 0; }

    /* TIGHT ROWS */
    .row {
        margin-bottom: 3px !important;
    }

    .mb-50 {
        margin-bottom: 4px !important;
    }

    /* TABLE COMPACT */
    table {
        width: 100%;
        font-size: 8px;
        table-layout: fixed;
        border-collapse: collapse;
    }

    th, td {
        padding: 2px 3px !important;
        vertical-align: middle;
        word-wrap: break-word;
    }

    /* REMOVE TABLER / BACKGROUND EFFECTS */
    body,
    body::before {
        background: none !important;
        filter: none !important;
        content: none !important;
    }

    .page,
    .page-wrapper {
        display: block !important;
        min-height: auto !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* HIDE NON-PRINT */
    header,
    nav,
    footer,
    .navbar,
    .d-print-none {
        display: none !important;
    }
    
}
</style>
    </head>
    <body>
        <div class="invoice-16 invoice-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="invoice-inner-9" id="invoice_wrapper">
                            <div class="invoice-top">
                                <div class="row">
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="logo">
                                            <h1 style="margin:0;">DYC Car Parts Trading & Rental Services</h1>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="invoice">
                                            <h1>
                                                Invoice # <span>{{ $order->invoice_no }}</span>
                                            </h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="invoice-info">
                                <div class="row">
                                    <div class="col-sm-6 mb-50">
                                        <div class="invoice-number">
                                            <h4 class="inv-title-1">
                                                Invoice date:
                                            </h4>
                                            <p class="invo-addr-1">
                                                {{ $order->order_date->format('F d Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 mb-50">
                                        <h4 class="inv-title-1">Customer</h4>
                                        <p class="inv-from-1">{{ $order->customer->name }}</p>
                                        <p class="inv-from-1">{{ $order->customer->phone }}</p>
                                        <p class="inv-from-1">{{ $order->customer->email }}</p>
                                        <p class="inv-from-2">{{ $order->customer->address }}</p>
                                    </div>
                                    <div class="col-sm-6 text-end mb-50">
                                        <h4 class="inv-title-1">Store</h4>
                                        <p class="inv-from-1">DYC Car Parts Trading & Rental Services</p>
                                        <p class="inv-from-1">+639755641064</p>
                                        <p class="inv-from-2">Abaga, Lala, Lanao del Norte, 9211, Philippines</p>
                                    </div>
                                </div>
                            </div>
                            <div class="order-summary">
                                <div class="table-outer">
                                    <table class="default-table invoice-table">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th class="text-center">Price</th>
                                                <th class="text-center">Qty</th>
                                                
                                                <th class="text-center">Total</th>
                                            </tr>
                                        </thead>

                                        <tbody>
{{--                                            @foreach ($orderDetails as $item)--}}
                                            @foreach ($order->details as $item)
                                            <tr>
                                                <td class="align-middle">
                                                    {{ $item->product->name }} - ({{ $item->product->brand?->name ?? 'No Brand' }}) - {{ $item->product->unit?->name ?? 'No Unit' }}
                                                </td>
                                                <td class="align-middle text-center">
                                                    {{ Number::currency(
                                                    ($item->unitcost) + ($item->markup_price ?? 0), 'PHP') }}
                                                </td>
                                                <td class="align-middle text-center">
                                                    {{ $item->quantity }}
                                                </td>
                                                
                                                <td class="align-middle text-center">
                                                    {{ Number::currency($item->total, 'PHP') }}
                                                </td>
                                            </tr>
                                            @endforeach

                                            <tr>
                                                <td colspan="3" class="text-end">
                                                    <strong>
                                                        Subtotal
                                                    </strong>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <strong>
                                                        {{ Number::currency($order->sub_total, 'PHP') }}
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end">
                                                    <strong>Tax</strong>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <strong>
                                                        {{ Number::currency($order->vat ?? 0, 'PHP') }}
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end">
                                                    <strong>Total</strong>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <strong>
                                                        {{ Number::currency($order->total, 'PHP') }}
                                                    </strong>
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
                        <div class="invoice-btn-section clearfix d-print-none">
                            <a href="javascript:window.print()" class="btn btn-lg btn-print">
                                <i class="fa fa-print"></i>
                                Print Invoice
                            </a>
                            <a id="invoice_download_btn" class="btn btn-lg btn-download">
                                <i class="fa fa-download"></i>
                                Download Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('assets/invoice/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/jspdf.min.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/html2canvas.js') }}"></script>
        <script src="{{ asset('assets/invoice/js/app.js') }}"></script>
    </body>
</html>
