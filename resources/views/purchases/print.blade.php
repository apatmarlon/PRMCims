<!DOCTYPE html>
<html>
<head>
    <title>Purchase Order #{{ $purchase->purchase_no }}</title>

    <!-- Tabler CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/tabler.min.css') }}">

    <style>
        /* Global look */
        body {
            background: #ffffff !important;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 14px;
            color: #000;
        }

        h2 {
            font-weight: 700;
            margin-bottom: 20px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Print Handling */
        @media print {
            .no-print { display: none !important; }
            .card { box-shadow: none !important; border: 1px solid #888 !important; }
            .table th { background: #f1f1f1 !important; }
        }

        .card {
            border: 1px solid #d7d7d7;
        }

        label {
            font-weight: 600;
            color: #444;
        }

        .form-control-solid {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 8px 10px;
        }

        .table th {
            background: #f1f5f9;
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
        }

        .table td, .table th {
            vertical-align: middle !important;
        }

        .photo img {
            max-height: 70px;
            border: 1px solid #ccc;
            padding: 3px;
            border-radius: 4px;
        }

        /* Force 2-column layout even in PRINT */
        .supplier-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .supplier-grid .col-box {
            width: 100%;
        }

        /* ===============================
           PRODUCT TABLE – FULL WIDTH, NO EXTRA SPACE
        ================================= */
        .table-responsive {
            padding: 0 !important;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 13px;
        }

        .product-table th {
            background: #e9eef5;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            padding: 6px 6px;
            border: 1px solid #bfc7d1;
        }

        .product-table td {
            padding: 6px 6px;
            border: 1px solid #cfd6df;
            vertical-align: middle;
        }

        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .amount {
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-sm {
            padding: 4px 8px;
            font-size: 12px;
        }

        /* Print optimization */
        @media print {
            .product-table th,
            .product-table td {
                padding: 5px 5px;
                font-size: 12px;
            }
        }

         /* ===============================
         SIGNATURE SECTION – FINAL FIX
        ================================= */
        .signature-section {
            width: 100%;
            margin-top: 60px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            column-gap: 80px;
            row-gap: 50px;
        }

        .signature-box {
            display: flex;
            flex-direction: column;
        }

        .signature-label-line {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .signature-label {
            font-weight: 600;
            margin-right: 10px;
            white-space: nowrap;
        }

        .signature-line {
            flex-grow: 1;
            border-bottom: 1px solid #000;
        }

        .signature-person {
            margin-top: 6px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
        }

        /* ---- Approved (CENTER) ---- */
        .signature-approved {
            grid-column: 1 / -1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .signature-approved .signature-label-line {
            justify-content: center;
        }

        .signature-approved .signature-line {
            width: 200px;       /* FIXED underline length */
            flex-grow: 0;
        }

    </style>
</head>

<body class="p-4">

    <!-- Header Buttons -->
    <div class="no-print mb-4 d-flex gap-2">
        <button type="submit" class="btn btn-success" onclick="window.print()">Print</button>
        <a class="btn btn-primary"  href="{{ URL::previous() }}">Back</a>
    </div>

    <!-- TITLE -->
    <div class="invoice-top mb-4">
        <div class="row">
            <div class="col-lg-6 col-sm-6">
                <div class="logo">
                    <h1>DYC Car Parts Trading & Rental Services</h1>
                </div>
                <p class="mb-0"><strong>Phone:</strong> +639755641064</p>
                <p class="mb-0">Abaga, Lala, Lanao del Norte, 9211, Philippines</p>
            </div>
        </div>
    </div>

    <hr class="mb-4">

    <!-- SUPPLIER INFORMATION -->
    <div class="card mb-4">
        <div class="card-header fw-bold">Supplier Information</div>
        <div class="card-body">
            <div class="supplier-grid">
                <div class="col-box">
                    <label>Name</label>
                    <div class="form-control form-control-solid mb-3">{{ $purchase->supplier->name }}</div>

                    <label>Purchase No.</label>
                    <div class="form-control form-control-solid mb-3">{{ $purchase->purchase_no }}</div>

                    <label>Order Date</label>
                    <div class="form-control form-control-solid mb-3">{{ $purchase->date ? $purchase->date->format('M d, Y') : 'N/A' }}</div>

                    <label>Total Amount</label>
                    <div class="form-control form-control-solid mb-3">{{ Number::currency($purchase->total_amount, 'PHP') }}</div>
                </div>

                <div class="col-box">
                    <label>Email</label>
                    <div class="form-control form-control-solid mb-3">{{ $purchase->supplier->email }}</div>

                    <label>Address</label>
                    <div class="form-control form-control-solid mb-3">{{ $purchase->supplier->address }}</div>

                    <label>Phone</label>
                    <div class="form-control form-control-solid mb-3">{{ $purchase->supplier->phone }}</div>

                    <label>Created By</label>
                    <div class="form-control form-control-solid mb-3">{{ $purchase->createdBy?->name ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- PRODUCT LIST -->
    <div class="card mb-4">
        <div class="card-header fw-bold">Product List</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th style="width:30%;">Product Name</th>
                            <th style="width:20%;">Brand</th>
                            <th style="width:10%;">Stock</th>
                            <th style="width:10%;">Qty</th>
                            <th style="width:15%;">Unit Price</th>
                            <th style="width:15%;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-left">{{ $product->product->name }}</td>
                            <td class="text-left">{{ $product->product->brand->name }}</td>
                            <td class="text-center"><span class="badge bg-warning badge-sm">{{ $product->product->quantity }}</span></td>
                            <td class="text-center"><span class="badge bg-success badge-sm">{{ $product->quantity }}</span></td>
                            <td class="text-right amount">
                                @if($product->is_freebie)
                                    <span class="badge bg-success badge-sm">FREE</span>
                                @else
                                    {{ Number::currency($product->unitcost, 'PHP') }}
                                @endif
                            </td>
                            <td class="text-right amount">
                                @if($product->is_freebie)
                                    <span class="badge bg-success badge-sm">FREE (₱0.00)</span>
                                @else
                                    <span class="badge bg-primary badge-sm">
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

    <div class="signature-section">

        <!-- Prepared By (LEFT) -->
        <div class="signature-box">
            <div class="signature-label-line">
                <span class="signature-label">Prepared By:</span>
                <span class="signature-line"></span>
            </div>
            <div class="signature-person">JOVETH LABARES</div>
        </div>

        <!-- Checked By (RIGHT) -->
        <div class="signature-box">
            <div class="signature-label-line">
                <span class="signature-label">Checked By:</span>
                <span class="signature-line"></span>
            </div>
            <div class="signature-person">JORDAN MARATAS</div>
        </div>

        <!-- Approved By (CENTERED BELOW) -->
        <div class="signature-box signature-approved">
            <div class="signature-label-line">
                <span class="signature-label">Approved By:</span>
                <span class="signature-line"></span>
            </div>
            <div class="signature-person">LYNDON G. CALICA</div>
        </div>

    </div>

    <!-- Auto Print -->
    <script>
        window.onload = () => window.print();
    </script>

</body>
</html>
