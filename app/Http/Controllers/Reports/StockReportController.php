<?php 

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Barryvdh\DomPDF\Facade\Pdf;

class StockReportController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->select('name', 'code', 'category_id', 'quantity', 'quantity_alert')
            ->orderBy('name')
            ->get();

        return view('reports.stock.index', compact('products'));
    }

    /* ---------------------------------------------------------
     | EXPORT EXCEL 
     | (PhpSpreadsheet - Same format as SupplierCustomerReportController)
    ----------------------------------------------------------*/
    public function exportExcel(Request $request)
    {
        $products = Product::with('category')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title
        $sheet->setCellValue('A1', 'STOCK REPORT');
        $sheet->fromArray(['Name', 'Code', 'Category', 'Quantity', 'Alert Qty'], NULL, 'A2');

        $row = 3;

        foreach ($products as $p) {
            $sheet->setCellValue("A$row", $p->name);
            $sheet->setCellValue("B$row", $p->code);
            $sheet->setCellValue("C$row", $p->category->name ?? '-');
            $sheet->setCellValue("D$row", $p->quantity);
            $sheet->setCellValue("E$row", $p->quantity_alert);
            $row++;
        }

        // Auto width
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Export Excel
        $writer = new Xls($spreadsheet);
        $filename = 'stock_report_' . date('Ymd-His') . '.xls';

        if (ob_get_length()) { 
            ob_end_clean(); 
        }

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /* ---------------------------------------------------------
     | EXPORT PDF (DomPDF)
    ----------------------------------------------------------*/
    public function exportPDF(Request $request)
    {
        $products = Product::with('category')->get();

        $pdf = Pdf::loadView('reports.stock.pdf', compact('products'));

        return $pdf->download('stock_report_' . date('Ymd-His') . '.pdf');
    }
}
