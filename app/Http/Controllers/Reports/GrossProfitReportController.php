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

class GrossProfitReportController extends Controller
{
    public function index(Request $request)
{
    $filterType = $request->filter_type ?? 'monthly';
    $startDate  = $request->start_date;
    $endDate    = $request->end_date;
    $year       = $request->year ?? Carbon::now()->year;
    $month      = $request->month ?? Carbon::now()->format('Y-m');

    // Initialize monthly chart arrays so they exist even if filterType != monthly
    $monthlyLabels      = [];
    $monthlyCost        = [];
    $monthlySales       = [];
    $monthlyGrossProfit = [];

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
        $dateQuery = [
            [DB::raw("DATE_FORMAT(order_details.order_date, '%Y-%m')"), '=', $month]
        ];
    }

    // --- Per Product Gross Profit Report ---
        $grossProfitReport = OrderDetails::leftJoin('products', 'products.id', '=', 'order_details.product_id')
        ->select(
            'products.name',
            DB::raw('SUM(order_details.quantity) AS quantity_sold'),

            // ✅ NET COST = buying_price * quantity
            DB::raw('SUM(products.buying_price * order_details.quantity) AS net_cost'),

            // Total Sales
            DB::raw('SUM(order_details.total) AS total_sales'),

            // Gross Profit = (selling - buying) * quantity
            DB::raw('SUM((order_details.unitcost - products.buying_price) * order_details.quantity) AS gross_profit')
        )
        ->where($dateQuery)
        ->groupBy('products.id', 'products.name')
        ->orderByDesc('gross_profit')
        ->get();

    // Totals
    $totalCost        = $grossProfitReport->sum('total_cost');
    $totalNetCost     = $grossProfitReport->sum('net_cost'); // Use net_cost instead of total_cost
    $totalSales       = $grossProfitReport->sum('total_sales');
    $totalGrossProfit = $grossProfitReport->sum('gross_profit');

    // Monthly Chart Data (only if relevant, but arrays always exist)
    $monthly = OrderDetails::join('products', 'products.id', '=', 'order_details.product_id')
        ->select(
            DB::raw("DATE_FORMAT(order_details.order_date, '%Y-%m') as month"),

            // ✅ NET COST
            DB::raw("SUM(products.buying_price * order_details.quantity) as net_cost"),

            DB::raw("SUM(order_details.total) as total_sales"),
            DB::raw("SUM((order_details.unitcost - products.buying_price) * order_details.quantity) as gross_profit")
        )
        ->groupBy('month')
        ->orderBy('month')
        ->get();


    $monthlyLabels      = $monthly->pluck('month');
    $monthlyNetCost     = $monthly->pluck('net_cost');
    $monthlySales       = $monthly->pluck('total_sales');
    $monthlyGrossProfit = $monthly->pluck('gross_profit');

    return view('reports.gross_profit', compact(
        'grossProfitReport',
        'monthlyLabels',
        'monthlyCost',
        'monthlySales',
        'monthlyGrossProfit',
        'totalCost',
        'totalNetCost',
        'totalSales',
        'totalGrossProfit',
        'filterType',
        'month',
        'year',
        'startDate',
        'endDate'
    ));
}

    // Excel Export
    public function exportExcel()
    {
        $grossProfitReport = OrderDetails::leftJoin('products', 'products.id', '=', 'order_details.product_id')
            ->select(
                'products.name',
                DB::raw('SUM(order_details.quantity) AS quantity_sold'),
                DB::raw('SUM(order_details.unitcost * order_details.quantity) AS total_cost'),
                DB::raw('SUM(order_details.total) AS total_sales'),
                DB::raw('SUM((order_details.unitcost - products.buying_price) * order_details.quantity) AS gross_profit')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('gross_profit')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['Product', 'Quantity Sold', 'Total Cost', 'Total Sales', 'Gross Profit']
        ]);

        foreach ($grossProfitReport as $index => $item) {
            $sheet->fromArray([
                [
                    $item->name,
                    $item->quantity_sold,
                    $item->total_cost,
                    $item->total_sales,
                    $item->gross_profit
                ]
            ], null, 'A' . ($index + 2));
        }

        $writer = new Xls($spreadsheet);
        $filename = 'gross-profit-report-' . now()->format('Ymd-His') . '.xls';

        if (ob_get_length()) ob_end_clean();

        return response()->streamDownload(fn() => $writer->save('php://output'), $filename, ['Content-Type' => 'application/vnd.ms-excel']);
    }

    // PDF Export
    public function exportPDF(Request $request)
    {
        $filterType = $request->filter_type ?? 'monthly';
        $startDate  = $request->start_date;
        $endDate    = $request->end_date;
        $year       = $request->year ?? Carbon::now()->year;
        $month      = $request->month ?? Carbon::now()->format('Y-m');

       $query = OrderDetails::join('products', 'products.id', '=', 'order_details.product_id')
            ->select(
                'products.name',
                DB::raw('SUM(order_details.quantity) AS quantity_sold'),

                // ✅ NET COST
                DB::raw('SUM(products.buying_price * order_details.quantity) AS net_cost'),

                DB::raw('SUM(order_details.total) AS total_sales'),
                DB::raw('SUM((order_details.unitcost - products.buying_price) * order_details.quantity) AS gross_profit')
            );

        if ($filterType === 'daily' && $startDate) {
            $endDate = $endDate ?: $startDate;
            $query->whereBetween('order_details.order_date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        } elseif ($filterType === 'yearly') {
            $query->whereYear('order_details.order_date', $year);
        } else {
            $query->whereYear('order_details.order_date', Carbon::parse($month)->year)
                  ->whereMonth('order_details.order_date', Carbon::parse($month)->month);
        }

        $grossProfitReport = $query->groupBy('products.id', 'products.name')
                                   ->havingRaw('SUM(order_details.quantity) > 0')
                                   ->orderByDesc('gross_profit')
                                   ->get();
        $totalNetCost = $grossProfitReport->sum('net_cost');

        return Pdf::loadView('reports.pdf.gross_profit', [
            'grossProfitReport' => $grossProfitReport,
            'totalCost'         => $grossProfitReport->sum('total_cost'),
            'totalSales'        => $grossProfitReport->sum('total_sales'),
            'totalGrossProfit'  => $grossProfitReport->sum('gross_profit'),
            'totalNetCost'      => $totalNetCost, // NEW
            'filterType'        => $filterType,
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'month'             => $month,
            'year'              => $year,
        ])->download('gross-profit-report.pdf');
    }
}
