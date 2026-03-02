<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sale Return #{{ $return->id }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        h2, h3 {
            margin: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
        }

        th, td {
            border: 1px solid #000;
            padding: 3px;
        }

        th {
            background: #f2f2f2;
            text-align: center;
        }

        .text-end { text-align: right; }
        .text-center { text-align: center; }

        .no-border td {
            border: none;
        }
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
<body>

<h2>DYC Car Parts Trading & Rental Services</h2>
<h3>SALE RETURN SLIP</h3>

<table class="no-border">
    <tr>
        <td>
            <strong>Return ID No:</strong> {{ $return->id }}<br>
            <strong>Invoice No:</strong> {{ $return->order->invoice_no }}<br>
            <strong>Date:</strong> {{ $return->created_at->format('F d, Y') }}
        </td>
        <td class="text-end">
            <strong>Customer</strong><br>
            {{ $return->customer->name }}<br>
            {{ $return->customer->phone }}<br>
            {{ $return->customer->address }}
        </td>
    </tr>
</table>

<br>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($return->details as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $item->product->name }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-end">{{ number_format($item->total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" class="text-end">Total Refund</th>
            <th class="text-end">
                {{ number_format($return->total_refund, 2) }}
            </th>
        </tr>
    </tfoot>
</table>

<br>

<strong>Return Reason:</strong><br>
<p>{{ $return->reason ?? 'N/A' }}</p>

<br>

<table style="width:100%; margin-top:60px; border:none;">
    <tr>
        <!-- Prepared By -->
        <td style="width:50%; border:none; padding-right:40px;">
            <div style="display:flex; align-items:center;">
                <strong style="white-space:nowrap; margin-right:10px;">Prepared By:</strong>
                <div style="flex:1; border-bottom:1px solid #000;"></div>
            </div>
            <div style="margin-top:6px; text-align:center; font-weight:700; text-transform:uppercase;">
                JOVETH LABARES
            </div>
        </td>

        <!-- Checked By -->
        <td style="width:50%; border:none; padding-left:40px;">
            <div style="display:flex; align-items:center;">
                <strong style="white-space:nowrap; margin-right:10px;">Checked By:</strong>
                <div style="flex:1; border-bottom:1px solid #000;"></div>
            </div>
            <div style="margin-top:6px; text-align:center; font-weight:700; text-transform:uppercase;">
                JORDAN MARATAS
            </div>
        </td>
    </tr>

    <!-- Approved By (CENTERED) -->
    <tr>
    <td colspan="2" style="border:none; padding-top:50px; text-align:center;">

        <table style="margin:0 auto; border:none;">
            <tr>
                <td style="border:none; font-weight:600; white-space:nowrap; padding-right:75px;">
                    Approved By:
                </td>

                <td style="border:none; margin-top:6px;">
                    <div style="width:250px; border-bottom:1px solid #000;"></div>
                </td>
            </tr>
        </table>

        <div style="margin-top:6px; font-weight:700; text-transform:uppercase;">
            LYNDON G. CALICA
        </div>

    </td>
</tr>
</table>


</body>
</html>
