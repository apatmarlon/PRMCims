<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SOA</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; }
        th { background: #eee; }
        .text-right { text-align: right; }
        .invoice-top {
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-info p {
            margin: 2px 0;
        }
    </style>
</head>
<body>
<div class="invoice-top">
    <div class="company-info">
        <div class="company-name">DYC Car Parts Trading & Rental Services</div>
        <p><strong>Phone:</strong> +639755641064</p>
        <p>Abaga, Lala, Lanao del Norte, 9211, Philippines</p>
    </div>
</div>
<h3><center>Statement of Account</center></h3>
<p><strong>Customer:</strong> {{ $customer->name }}</p>
<p><strong>Address:</strong> {{ $customer->address }}</p>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Ref #</th>
            <th>Due Date</th>
            <th>Description</th>
            <th class="text-right">Debit</th>
            <th class="text-right">Credit</th>
            <th class="text-right">Balance</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="5"><strong>Beginning Balance</strong></td>
            <td class="text-right">{{ number_format($soa->beginning_balance, 2) }}</td>
        </tr>

        @foreach($soa->transactions as $txn)
        <tr>
            <td>{{ $txn->transaction_date }}</td>
            <td>{{ $txn->ref_no }}</td>
            <td>{{ $txn->due_date ?? '-' }}</td>
            <td>{{ $txn->description }}</td>
            <td class="text-right">{{ number_format($txn->debit, 2) }}</td>
            <td class="text-right">{{ number_format($txn->credit, 2) }}</td>
            <td class="text-right">{{ number_format($txn->balance, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<br><br>

<table width="100%" cellpadding="4">
    <tr>
        <td width="70%" align="right"><strong>TOTAL RECEIVABLE</strong></td>
        <td width="30%" align="right">₱ {{ number_format($totals['totalReceivable'], 2) }}</td>
    </tr>
    <tr>
        <td align="right"><strong>LESS TOTAL PAYMENT</strong></td>
        <td align="right">₱ {{ number_format($totals['totalPayment'], 2) }}</td>
    </tr>
    <tr>
        <td align="right"><strong>TOTAL AMOUNT DUE</strong></td>
        <td align="right">
            <strong>₱ {{ number_format($totals['totalAmountDue'], 2) }}</strong>
        </td>
    </tr>
    <tr>
        <td align="right"><strong>TOTAL AMOUNT OVERDUE</strong></td>
        <td align="right">₱ {{ number_format($totals['totalOverdue'], 2) }}</td>
    </tr>
    <tr>
        <td align="right"><strong>TOTAL AMOUNT BEFORE DUE DATE</strong></td>
        <td align="right">₱ {{ number_format($totals['totalBeforeDueDate'], 2) }}</td>
    </tr>
</table>
<br><br><br>
<table width="100%" style="border: none; text-align: center;">
    <tr>
        <td width="33%" style="border: none;">
            <strong>Prepared By:</strong>
            <br><br>
            <u>Regin Mae H. Alfeche</u><br>
            <strong>Warehouse Supervisor</strong>
        </td>

        <td width="33%" style="border: none;">
            <strong>Checked By:</strong>
            <br><br>
            <u>Jordan Maratas</u><br>
            <strong>Partsman</strong>
        </td>

        <td width="33%" style="border: none;">
            <strong>Received By:</strong>
            <br><br>
            <u>Jeshell Mae Canoy</u><br>
            <strong>Store Manager</strong>
        </td>
    </tr>
</table>
</body>
</html>
