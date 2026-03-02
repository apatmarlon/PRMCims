<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\OrderDetails;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProductOrderReportController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderDetails::with(['product']);

        // 🔹 Filter by product
        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        // 🔹 Date filters
        if ($request->type === 'daily') {
            $query->whereDate('order_date', today());
        }

        if ($request->type === 'monthly') {
            $query->whereMonth('order_date', now()->month)
                ->whereYear('order_date', now()->year);
        }

        if ($request->type === 'yearly') {
            $query->whereYear('order_date', now()->year);
        }

        if ($request->from && $request->to) {
            $query->whereBetween('order_date', [
                Carbon::parse($request->from)->startOfDay(),
                Carbon::parse($request->to)->endOfDay()
            ]);
        }

        $productName = $request->product_name;

        if ($productName) {
            $query->whereHas('product', function ($q) use ($productName) {
                $q->where('name', 'like', "%{$productName}%");
            });
        }

        // 🔹 Get records
        $records = $query->latest()->get();

        // 🔹 Totals
        $totalGrossProfit = $records->sum(function ($row) {
            $buyingPrice = $row->product?->buying_price ?? 0;
            $sellingPrice = $row->unitcost;
            return ($sellingPrice - $buyingPrice) * $row->quantity;
        });

        $totalNetCost = $records->sum(function ($row) {
            $unitCost = $row->product?->buying_price ?? 0;
            $qty = $row->quantity ?? 0;
            return $unitCost * $qty;
        });

        return view('reports.product-orders', [
            'records'        => $records,
            'products'       => Product::all(),
            'productName'    => $productName,
            'totalQty'       => $records->sum('quantity'),
            'totalSales'     => $records->sum('total'),
            'totalGrossProfit' => $totalGrossProfit,
            'totalNetCost'   => $totalNetCost,
        ]);
    }


    // ================= PDF =================
   public function pdf(Request $request)
    {
        $records = $this->filteredData($request);

        // Calculate totals
        $totalQty         = $records->sum('quantity');
        $totalSales       = $records->sum('total');
        $totalGrossProfit = $records->sum(function ($row) {
            $buyingPrice = $row->product?->buying_price ?? 0;
            $sellingPrice = $row->unitcost ?? 0;
            return ($sellingPrice - $buyingPrice) * $row->quantity;
        });
        $totalNetCost = $records->sum(function ($row) {
            $unitCost = $row->product?->buying_price ?? 0;
            $qty = $row->quantity ?? 0;
            return $unitCost * $qty;
        });

        $pdf = Pdf::loadView('reports.product-orders-pdf', [
            'records'          => $records,
            'totalQty'         => $totalQty,
            'totalSales'       => $totalSales,
            'totalGrossProfit' => $totalGrossProfit,
            'totalNetCost'     => $totalNetCost,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('product-order-report.pdf');
    }


    // ================= EXCEL =================

public function excel(Request $request)
{
    $records = $this->filteredData($request);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // ===== HEADER =====
    $sheet->fromArray([
        ['Date', 'Product', 'Brand', 'Quantity', 'Unit Cost', 'Selling Price', 'Net Cost', 'Gross Profit', 'Total']
    ]);

    $row = 2;

    $totalQty         = 0;
    $totalSales       = 0;
    $totalNetCost     = 0;
    $totalGrossProfit = 0;

    $currencyFormat = '"₱"#,##0.00;[Red]\-"₱"#,##0.00';

    foreach ($records as $item) {
        $unitCost     = $item->product?->buying_price ?? 0;
        $sellingPrice = $item->unitcost ?? 0;
        $qty          = $item->quantity ?? 0;

        $netCost      = $unitCost * $qty;             // NEW
        $grossProfit  = ($sellingPrice - $unitCost) * $qty;

        $sheet->fromArray([
            $item->created_at->format('Y-m-d'),
            $item->product?->name ?? 'Deleted Product',
            $item->product?->brand?->name ?? 'N/A',
            $qty,
            $unitCost,
            $sellingPrice,
            $netCost,          // NEW COLUMN
            $grossProfit,
            $item->total
        ], null, "A{$row}");

        // Format currency columns: Unit Cost, Selling Price, Net Cost, Gross Profit, Total
        foreach (['E','F','G','H','I'] as $col) {
            $sheet->getStyle("{$col}{$row}")
                ->getNumberFormat()
                ->setFormatCode($currencyFormat);
        }

        $totalQty         += $qty;
        $totalNetCost     += $netCost;         // NEW
        $totalSales       += $item->total;
        $totalGrossProfit += $grossProfit;

        $row++;
    }

    // ===== SUMMARY =====
    $sheet->setCellValue("A{$row}", 'TOTAL SUMMARY');
    $sheet->mergeCells("A{$row}:C{$row}"); // Merge Date + Product + Brand columns

    $sheet->setCellValue("D{$row}", $totalQty);
    $sheet->setCellValue("E{$row}", '');
    $sheet->setCellValue("F{$row}", '');
    $sheet->setCellValue("G{$row}", $totalNetCost);       // NEW
    $sheet->setCellValue("H{$row}", $totalGrossProfit);
    $sheet->setCellValue("I{$row}", $totalSales);

    // Format summary currency
    foreach (['G','H','I'] as $col) {
        $sheet->getStyle("{$col}{$row}")
            ->getNumberFormat()
            ->setFormatCode($currencyFormat);
    }

    $writer = new Xlsx($spreadsheet);

    return response()->streamDownload(
        fn () => $writer->save('php://output'),
        'product-order-report.xlsx'
    );
}



    // ================= FILTER HELPER =================
   private function filteredData(Request $request)
    {
        $query = OrderDetails::with('product');

        // 🔹 Filter by product_id (BEST & FASTEST)
        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        // 🔹 Filter by product name (text search)
        if ($request->product_name) {
            $productName = $request->product_name;

            $query->whereHas('product', function ($q) use ($productName) {
                $q->where('name', 'like', "%{$productName}%");
            });
        }

        // 🔹 Date range
        if ($request->from && $request->to) {
            $query->whereBetween('order_date', [
                Carbon::parse($request->from)->startOfDay(),
                Carbon::parse($request->to)->endOfDay()
            ]);
        }

        // 🔹 Type filters
        if ($request->type === 'daily') {
            $query->whereDate('order_date', today());
        }

        if ($request->type === 'monthly') {
            $query->whereMonth('order_date', now()->month)
                ->whereYear('order_date', now()->year);
        }

        if ($request->type === 'yearly') {
            $query->whereYear('order_date', now()->year);
        }

        return $query->latest()->get();
    }
    public function search(Request $request)
    {
        $term = $request->get('term', '');

        $products = Product::with('brand')
            ->where('name', 'like', "%{$term}%")
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json(
            $products->map(fn ($p) => [
                'value' => $p->name,
                'label' => $p->name . ' (' . ($p->brand?->name ?? 'N/A') . ')'
            ])
        );
    }
}
