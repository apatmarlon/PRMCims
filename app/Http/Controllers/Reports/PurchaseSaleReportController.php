<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\OrderDetails;
use App\Models\PurchaseDetails;
use App\Models\Purchase;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseSaleReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate   = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');

        // Purchases
        $purchases = PurchaseDetails::select(
            'products.name as product_name',
            DB::raw('SUM(purchase_details.quantity) as quantity'),
            DB::raw('SUM(purchase_details.total) as total')
        )
        ->join('products', 'purchase_details.product_id', '=', 'products.id')
        ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
        ->whereBetween('purchases.date', [$startDate, $endDate])
        ->groupBy('products.name')
        ->get();

        // Sales
        $sales = OrderDetails::select(
            'products.name as product_name',
            DB::raw('SUM(order_details.quantity) as quantity'),
            DB::raw('SUM(order_details.total) as total')
        )
        ->join('products', 'order_details.product_id', '=', 'products.id')
        ->join('orders', 'order_details.order_id', '=', 'orders.id')
        ->whereBetween('orders.order_date', [$startDate, $endDate])
        ->groupBy('products.name')
        ->get();

        // Chart data
        $chartLabels = $sales->pluck('product_name');
        $salesData = $sales->pluck('total');
        $purchaseData = $purchases->pluck('total');

        return view('reports.purchase-sale', compact(
            'purchases', 'sales', 'chartLabels', 'salesData', 'purchaseData', 'startDate', 'endDate'
        ));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        $sales = OrderDetails::select(
            'products.name as product_name',
            DB::raw('SUM(order_details.quantity) as quantity'),
            DB::raw('SUM(order_details.total) as total')
        )
        ->join('products', 'order_details.product_id', '=', 'products.id')
        ->join('orders', 'order_details.order_id', '=', 'orders.id')
        ->whereBetween('orders.order_date', [$startDate, $endDate])
        ->groupBy('products.name')
        ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['Product', 'Quantity', 'Total'], NULL, 'A1');

        $row = 2;
        foreach ($sales as $s) {
            $sheet->setCellValue("A$row", $s->product_name);
            $sheet->setCellValue("B$row", $s->quantity);
            $sheet->setCellValue("C$row", $s->total);
            $row++;
        }

        $writer = new Xls($spreadsheet);
        $filename = 'sales-report-' . date('Ymd-His') . '.xls';
        if (ob_get_length()) { ob_end_clean(); }
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        $sales = OrderDetails::select(
            'products.name as product_name',
            DB::raw('SUM(order_details.quantity) as quantity'),
            DB::raw('SUM(order_details.total) as total')
        )
        ->join('products', 'order_details.product_id', '=', 'products.id')
        ->join('orders', 'order_details.order_id', '=', 'orders.id')
        ->whereBetween('orders.order_date', [$startDate, $endDate])
        ->groupBy('products.name')
        ->get();

        $pdf = PDF::loadView('reports.purchase-sale-combined-pdf', compact('sales', 'startDate', 'endDate'));
        return $pdf->download('sales-report-' . date('Ymd-His') . '.pdf');
    }
    public function exportExcelCombined(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        // Fetch Purchases
        $purchases = PurchaseDetails::select(
            'products.name as product_name',
            DB::raw('SUM(purchase_details.quantity) as quantity'),
            DB::raw('SUM(purchase_details.total) as total')
        )
        ->join('products', 'purchase_details.product_id', '=', 'products.id')
        ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
        ->whereBetween('purchases.date', [$startDate, $endDate])
        ->groupBy('products.name')
        ->get();

        // Fetch Sales
        $sales = OrderDetails::select(
            'products.name as product_name',
            DB::raw('SUM(order_details.quantity) as quantity'),
            DB::raw('SUM(order_details.total) as total')
        )
        ->join('products', 'order_details.product_id', '=', 'products.id')
        ->join('orders', 'order_details.order_id', '=', 'orders.id')
        ->whereBetween('orders.order_date', [$startDate, $endDate])
        ->groupBy('products.name')
        ->get();

        // Create Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Purchases Header
        $sheet->setCellValue('A1', 'PURCHASES');
        $sheet->fromArray(['Product', 'Quantity', 'Total'], NULL, 'A2');
        $row = 3;
        foreach ($purchases as $p) {
            $sheet->setCellValue("A$row", $p->product_name);
            $sheet->setCellValue("B$row", $p->quantity);
            $sheet->setCellValue("C$row", $p->total);
            $row++;
        }

        $row += 2; // empty line

        // Sales Header
        $sheet->setCellValue("A$row", 'SALES');
        $sheet->fromArray(['Product', 'Quantity', 'Total'], NULL, "A" . ($row + 1));
        $row += 2;
        foreach ($sales as $s) {
            $sheet->setCellValue("A$row", $s->product_name);
            $sheet->setCellValue("B$row", $s->quantity);
            $sheet->setCellValue("C$row", $s->total);
            $row++;
        }

        // Auto column width
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xls($spreadsheet);
        $filename = 'purchase-sale-report-' . date('Ymd-His') . '.xls';

        if (ob_get_length()) { ob_end_clean(); }

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
    public function exportPdfCombined(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        $purchases = PurchaseDetails::select(
            'products.name as product_name',
            DB::raw('SUM(purchase_details.quantity) as quantity'),
            DB::raw('SUM(purchase_details.total) as total')
        )
        ->join('products', 'purchase_details.product_id', '=', 'products.id')
        ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
        ->whereBetween('purchases.date', [$startDate, $endDate])
        ->groupBy('products.name')
        ->get();

        $sales = OrderDetails::select(
            'products.name as product_name',
            DB::raw('SUM(order_details.quantity) as quantity'),
            DB::raw('SUM(order_details.total) as total')
        )
        ->join('products', 'order_details.product_id', '=', 'products.id')
        ->join('orders', 'order_details.order_id', '=', 'orders.id')
        ->whereBetween('orders.order_date', [$startDate, $endDate])
        ->groupBy('products.name')
        ->get();

        $pdf = PDF::loadView('reports.purchase-sale-combined-pdf', compact('purchases', 'sales', 'startDate', 'endDate'));
        return $pdf->download('purchase-sale-report-' . date('Ymd-His') . '.pdf');
    }

}
