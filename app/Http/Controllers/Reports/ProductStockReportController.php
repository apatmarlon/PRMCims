<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductStockReportController extends Controller
{
    public function index(Request $request)
    {
        $filterType = $request->filter_type ?? 'monthly';
        $startDate  = $request->start_date;
        $endDate    = $request->end_date;
       $month = $request->month ?? now()->format('Y-m');
        $year       = $request->year ?? now()->year;
        $minQty     = $request->min_qty;
        $maxQty     = $request->max_qty;
        $customerId = $request->customer_id;

        $query = Product::with('customer');

        if ($minQty !== null) {
            $query->where('quantity', '>=', $minQty);
        }

        if ($maxQty !== null) {
            $query->where('quantity', '<=', $maxQty);
        }

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }
        $productName = $request->product_name;

        if($productName) {
            $query->where('name', 'like', "%{$productName}%");
        }
        // DAILY FILTER
            if ($filterType == 'daily' && $startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            // MONTHLY FILTER
            if ($filterType == 'monthly' && $month) {

                $date = explode('-', $month);

                if(count($date) == 2) {
                    $query->whereYear('created_at', $date[0])
                        ->whereMonth('created_at', $date[1]);
                }
            }

            // YEARLY FILTER
            if ($filterType == 'yearly' && $year) {
                $query->whereYear('created_at', $year);
            }
        $products = $query->orderBy('quantity')->get();

        return view('reports.product', [
            'products'      => $products,
            'customers'     => Customer::select('id', 'name')->orderBy('name')->get(),
            'minQty'        => $minQty,
            'maxQty'        => $maxQty,
            'productName'   => $productName,
            'customerId'    => $customerId,
            'totalProducts' => $products->count(),
            'totalQuantity' => $products->sum('quantity'),

            // ADD THESE
            'filterType'    => $filterType,
            'startDate'     => $startDate,
            'endDate'       => $endDate,
            'month'         => $month,
            'year'          => $year,
        ]);
    }

    public function exportPDF(Request $request)
{
    $query = Product::with('customer');

    // Quantity filters
    if ($request->min_qty !== null) {
        $query->where('quantity', '>=', $request->min_qty);
    }
    if ($request->max_qty !== null) {
        $query->where('quantity', '<=', $request->max_qty);
    }

    if ($request->customer_id) {
        $query->where('customer_id', $request->customer_id);
    }

    // Product name filter
    $productName = $request->product_name;
    if($productName) {
        $query->where('name', 'like', "%{$productName}%");
    }
    // DAILY FILTER
    if ($filterType == 'daily' && $startDate && $endDate) {
        $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // MONTHLY FILTER
    if ($filterType == 'monthly' && $month) {

        $date = explode('-', $month);

        if(count($date) == 2) {
            $query->whereYear('created_at', $date[0])
                ->whereMonth('created_at', $date[1]);
        }
    }

    // YEARLY FILTER
    if ($filterType == 'yearly' && $year) {
        $query->whereYear('created_at', $year);
    }
    $products = $query->orderBy('quantity')->get();

    $customer = $request->customer_id
        ? Customer::find($request->customer_id)
        : null;

    $pdf = Pdf::loadView('reports.pdf.product-stock-pdf', [
        'products' => $products,
        'customer' => $customer,
        'minQty'   => $request->min_qty,
        'maxQty'   => $request->max_qty,
        'count'    => $products->count(),
        'totalQty' => $products->sum('quantity'),
    ]);

    return $pdf->download('product-stock-report.pdf');
}

    public function exportExcel(Request $request)
{
    $query = Product::with('customer');

    // Quantity filters
    if ($request->min_qty !== null) {
        $query->where('quantity', '>=', $request->min_qty);
    }

    if ($request->max_qty !== null) {
        $query->where('quantity', '<=', $request->max_qty);
    }

    if ($request->customer_id) {
        $query->where('customer_id', $request->customer_id);
    }

    // Product name filter
    $productName = $request->product_name;
    if($productName) {
        $query->where('name', 'like', "%{$productName}%");
    }

    // DAILY FILTER
    if ($filterType == 'daily' && $startDate && $endDate) {
        $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // MONTHLY FILTER
    if ($filterType == 'monthly' && $month) {

        $date = explode('-', $month);

        if(count($date) == 2) {
            $query->whereYear('created_at', $date[0])
                ->whereMonth('created_at', $date[1]);
        }
    }

    // YEARLY FILTER
    if ($filterType == 'yearly' && $year) {
        $query->whereYear('created_at', $year);
    }
    $products = $query->orderBy('quantity')->get();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $sheet->fromArray([
        ['Product', 'Brand', 'Customer', 'Quantity', 'Alert Level'],
    ], null, 'A1');

    // Data
    $row = 2;
    foreach ($products as $product) {
        $sheet->setCellValue("A{$row}", $product->name);
        $sheet->setCellValue("B{$row}", $product->brand?->name ?? 'N/A');
        $sheet->setCellValue("C{$row}", $product->customer?->name ?? 'N/A');
        $sheet->setCellValue("D{$row}", $product->quantity);
        $sheet->setCellValue("E{$row}", $product->quantity_alert);
        $row++;
    }

    $filename = 'product-stock-report-' . now()->format('Ymd-His') . '.xlsx';

    $response = new StreamedResponse(function() use ($spreadsheet) {
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    });

    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");

    return $response;
}

    public function search(Request $request)
    {
        $term = $request->get('term', '');

        // Get product names with brand
        $products = Product::with('brand')
                    ->where('name', 'like', "%{$term}%")
                    ->orderBy('name')
                    ->limit(10)
                    ->get(['id', 'name', 'brand_id']);

        // Format suggestions: "Product Name (Brand)"
       $suggestions = $products->map(function($product) {
        $brand = $product->brand?->name ?? 'N/A';
        return [
            'value' => $product->name,       // actual value for input
            'label' => "{$product->name} ({$brand})" // what user sees
        ];
    });
    return response()->json($suggestions);
    }
}