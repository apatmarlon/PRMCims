<?php 

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplierCustomerReportController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::select('name', 'email')
            ->orderBy('name')
            ->get();

        $suppliers = Supplier::select('name', 'email', 'shopname', 'type')
            ->orderBy('name')
            ->get();

        return view('reports.supplier-customer', compact('customers', 'suppliers'));
    }

    /* ---------------------------------------------------------
     | EXPORT EXCEL 
     | (PhpSpreadsheet - just like PurchaseSaleReportController)
    ----------------------------------------------------------*/
    public function exportExcel(Request $request)
    {
        $customers = Customer::all();
        $suppliers = Supplier::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title
        $sheet->setCellValue('A1', 'CUSTOMERS');
        $sheet->fromArray(['Name', 'Email'], NULL, 'A2');

        $row = 3;
        foreach ($customers as $c) {
            $sheet->setCellValue("A$row", $c->name);
            $sheet->setCellValue("B$row", $c->email);
            $row++;
        }

        $row += 2; // spacing

        // Suppliers Section
        $sheet->setCellValue("A$row", 'SUPPLIERS');
        $sheet->fromArray(['Name', 'Email', 'Shop Name', 'Type'], NULL, "A" . ($row + 1));

        $row += 2;

        foreach ($suppliers as $s) {
            $sheet->setCellValue("A$row", $s->name);
            $sheet->setCellValue("B$row", $s->email);
            $sheet->setCellValue("C$row", $s->shopname);
            $sheet->setCellValue("D$row", $s->type->value);
            $row++;
        }

        // Auto width
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Export Excel
        $writer = new Xls($spreadsheet);
        $filename = 'supplier_customer_report_' . date('Ymd-His') . '.xls';

        if (ob_get_length()) { ob_end_clean(); }

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
        $customers = Customer::all();
        $suppliers = Supplier::all();

        $pdf = Pdf::loadView('reports.pdf.supplier-customer', compact('customers', 'suppliers'));

        return $pdf->download('supplier_customer_report_' . date('Ymd-His') . '.pdf');
    }
}
