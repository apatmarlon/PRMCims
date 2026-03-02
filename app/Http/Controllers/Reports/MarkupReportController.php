<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MarkupReportController extends Controller
{
    public function index(Request $request)
    {
        // --- DATE FILTERS ---
        $filterType = $request->filter_type ?? 'monthly';
        $startDate  = $request->start_date;
        $endDate    = $request->end_date;
        $year       = $request->year ?? Carbon::now()->year;
        $month      = $request->month ?? Carbon::now()->format('Y-m');

        if ($filterType === 'daily' && $startDate && $endDate) {
            $dateQuery = [
                ['order_details.order_date', '>=', $startDate . ' 00:00:00'],
                ['order_details.order_date', '<=', $endDate . ' 23:59:59'],
            ];
        } elseif ($filterType === 'yearly') {
            $dateQuery = [
                [DB::raw('YEAR(order_details.order_date)'), '=', $year]
            ];
        } else {
            // Monthly (default)
            $dateQuery = [
                [DB::raw("DATE_FORMAT(order_details.order_date, '%Y-%m')"), '=', $month]
            ];
        }

        // --- PER PRODUCT MARKUP REPORT ---
        $markupReport = OrderDetails::leftJoin('products', 'products.id', '=', 'order_details.product_id')
            ->select(
                'products.name',
                DB::raw('SUM(order_details.quantity) AS quantity_sold'),

                // sales WITHOUT markup
                DB::raw('SUM(order_details.total - (order_details.markup_price * order_details.quantity)) AS sales_without_markup'),

                // markup only
                DB::raw('SUM(order_details.markup_price * order_details.quantity) AS total_markup'),

                // sales WITH markup
                DB::raw('SUM(order_details.total) AS sales_with_markup')
            )
            ->where($dateQuery)
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sales_with_markup')
            ->get();

        // --- TOTALS ---
        $totalSalesWithoutMarkup = $markupReport->sum('sales_without_markup');
        $totalSalesWithMarkup    = $markupReport->sum('sales_with_markup');
        $totalMarkup             = $markupReport->sum('total_markup');

        $monthly = OrderDetails::select(
        DB::raw("DATE_FORMAT(order_details.order_date, '%Y-%m') as month"),
        DB::raw("SUM(total - (markup_price * quantity)) as sales_without_markup"),
        DB::raw("SUM(markup_price * quantity) as total_markup"),
        DB::raw("SUM(total) as sales_with_markup")
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyLabels = $monthly->pluck('month');
        $monthlyWithoutMarkup = $monthly->pluck('sales_without_markup');
        $monthlyMarkup = $monthly->pluck('total_markup');
        $monthlyWithMarkup = $monthly->pluck('sales_with_markup');


        return view('reports.markup', compact(
            'markupReport',
            'monthly',
            'monthlyLabels',
            'monthlyWithoutMarkup',
            'monthlyMarkup',
            'monthlyWithMarkup',
            'totalSalesWithoutMarkup',
            'totalSalesWithMarkup',
            'totalMarkup',
            'filterType',
            'month',
            'year',
            'startDate',
            'endDate'
        ));

    }

    // --- EXPORT EXCEL ---
    public function exportExcel()
    {
        $markupReport = OrderDetails::leftJoin('products', 'products.id', '=', 'order_details.product_id')
            ->select(
                'products.name',
                DB::raw('SUM(order_details.quantity) AS quantity_sold'),
                DB::raw('SUM(order_details.total - (order_details.markup_price * order_details.quantity)) AS sales_without_markup'),
                DB::raw('SUM(order_details.markup_price * order_details.quantity) AS total_markup'),
                DB::raw('SUM(order_details.total) AS sales_with_markup')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sales_with_markup')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->fromArray([
            ['Product', 'Quantity Sold', 'Sales Without Markup', 'Total Markup', 'Sales With Markup']
        ]);

        foreach ($markupReport as $index => $item) {
            $sheet->fromArray([
                [
                    $item->name,
                    $item->quantity_sold,
                    $item->sales_without_markup,
                    $item->total_markup,
                    $item->sales_with_markup
                ]
            ], null, 'A' . ($index + 2));
        }

        $writer = new Xls($spreadsheet);
        $filename = 'markup-report-' . now()->format('Ymd-His') . '.xls';

        if (ob_get_length()) {
            ob_end_clean();
        }

        return response()->streamDownload(
            fn () => $writer->save('php://output'),
            $filename,
            ['Content-Type' => 'application/vnd.ms-excel']
        );
    }

    // --- EXPORT PDF ---
    public function exportPDF(Request $request)
    {
        $filterType = $request->filter_type ?? 'monthly';
        $startDate  = $request->start_date;
        $endDate    = $request->end_date;
        $year       = $request->year ?? Carbon::now()->year;
        $month      = $request->month ?? Carbon::now()->format('Y-m');

        // Use INNER JOIN so only products with orders appear
        $query = OrderDetails::join('products', 'products.id', '=', 'order_details.product_id')
            ->select(
                'products.name',
                DB::raw('SUM(order_details.quantity) AS quantity_sold'),
                DB::raw('SUM(order_details.total - (order_details.markup_price * order_details.quantity)) AS sales_without_markup'),
                DB::raw('SUM(order_details.markup_price * order_details.quantity) AS total_markup'),
                DB::raw('SUM(order_details.total) AS sales_with_markup')
            );

        // Apply date filters
        if ($filterType === 'daily' && $startDate) {
            $endDate = $endDate ?: $startDate; // handle same-day filter
            $query->whereBetween('order_details.order_date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        } elseif ($filterType === 'yearly') {
            $query->whereYear('order_details.order_date', $year);
        } else {
            // Monthly
            $query->whereYear('order_details.order_date', Carbon::parse($month)->year)
                ->whereMonth('order_details.order_date', Carbon::parse($month)->month);
        }

        // Group and filter out zero-quantity products
        $markupReport = $query->groupBy('products.id', 'products.name')
                            ->havingRaw('SUM(order_details.quantity) > 0')
                            ->orderByDesc('sales_with_markup')
                            ->get();

        return Pdf::loadView('reports.pdf.markup', [
            'markupReport'            => $markupReport,
            'totalSalesWithoutMarkup' => $markupReport->sum('sales_without_markup'),
            'totalSalesWithMarkup'    => $markupReport->sum('sales_with_markup'),
            'totalMarkup'             => $markupReport->sum('total_markup'),
            'filterType'              => $filterType,
            'startDate'               => $startDate,
            'endDate'                 => $endDate,
            'month'                   => $month,
            'year'                    => $year,
        ])->download('markup-report.pdf');
    }
}