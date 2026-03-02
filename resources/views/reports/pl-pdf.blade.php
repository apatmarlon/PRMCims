<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        /* HEADER */
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

        /* TITLE */
        h2 {
            text-align: center;
            margin-top: 20px;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        td {
            padding: 10px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>

<!-- INVOICE-STYLE HEADER -->
<div class="invoice-top">
    <div class="company-info">
        <div class="company-name">
            DYC Car Parts Trading & Rental Services
        </div>
        <p><strong>Phone:</strong> +639755641064</p>
        <p>Abaga, Lala, Lanao del Norte, 9211, Philippines</p>
    </div>
</div>

<h2>Profit & Loss Report</h2>

<table>
    <tr>
        <td><b>Total Sales</b></td>
        <td>{{ number_format($sales, 2) }}</td>
    </tr>
    <tr>
        <td><b>Cost of Goods Sold</b></td>
        <td>{{ number_format($cogs, 2) }}</td>
    </tr>
    <tr>
        <td><b>Net Profit</b></td>
        <td>{{ number_format($profit, 2) }}</td>
    </tr>
</table>

</body>
</html>
