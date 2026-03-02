<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\OrderDetails;
use App\Models\PurchaseDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ProfitLossController extends Controller
{
    public function index(Request $request)
    {
        // --- DATE FILTERS ---
      $filterType = $request->filter_type ?? 'monthly';
        $startDate  = $request->start_date;
        $endDate    = $request->end_date;

        // Always define these
        $year  = $request->year  ?? Carbon::now()->year;
        $month = $request->month ?? Carbon::now()->format('Y-m');

        // Auto-generate date range
        if ($filterType == 'daily' && $startDate && $endDate) {
            $dateQuery = [
                ['order_details.order_date', '>=', $startDate . " 00:00:00"],
                ['order_details.order_date', '<=', $endDate . " 23:59:59"],
            ];
        } elseif ($filterType == 'yearly') {
            $dateQuery = [
                [DB::raw("YEAR(order_details.order_date)"), '=', $year]
            ];
        } else {
            // monthly default
            $dateQuery = [
                [DB::raw("DATE_FORMAT(order_details.order_date, '%Y-%m')"), '=', $month]
            ];
        }

        // --- TOTAL SALES ---
        $sales = OrderDetails::where($dateQuery)->sum('total');

        // --- COGS ---
        $cogsDateQuery = [];

        if ($filterType == 'daily' && $startDate && $endDate) {
            $cogsDateQuery = [
                ['purchase_details.created_at', '>=', $startDate . " 00:00:00"],
                ['purchase_details.created_at', '<=', $endDate . " 23:59:59"],
            ];
        } elseif ($filterType == 'yearly') {
            $cogsDateQuery = [
                [DB::raw("YEAR(purchase_details.created_at)"), '=', $year]
            ];
        } else {
            $cogsDateQuery = [
                [DB::raw("DATE_FORMAT(purchase_details.created_at, '%Y-%m')"), '=', $month]
            ];
        }

        $cogs = PurchaseDetails::where($cogsDateQuery)
            ->sum(DB::raw('quantity * unitcost'));

        $profit = $sales - $cogs;

        // --- MONTHLY TREND ---
        $monthly = OrderDetails::select(
            DB::raw("DATE_FORMAT(order_details.order_date, '%Y-%m') as month"),
            DB::raw("SUM(total) as sales")
        )
        ->groupBy('month')->orderBy('month')->get();

        $monthlyCogs = PurchaseDetails::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw("SUM(quantity * unitcost) as cogs")
        )
        ->groupBy('month')->orderBy('month')->get();

        // --- PER PRODUCT PROFIT ---
        $productProfit = OrderDetails::leftJoin('products', 'products.id', '=', 'order_details.product_id')
            ->leftJoin('purchase_details', 'purchase_details.product_id', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('SUM(order_details.total) as sales'),
                DB::raw('SUM(purchase_details.quantity * purchase_details.unitcost) as cogs'),
                DB::raw('SUM(order_details.total) - SUM(purchase_details.quantity * purchase_details.unitcost) as profit')
            )
            ->where($dateQuery)
            ->groupBy('products.name')
            ->orderBy('profit', 'DESC')
            ->get();

        return view('reports.profit-loss', compact(
            'sales', 'cogs', 'profit',
            'monthly', 'monthlyCogs',
            'productProfit',
            'filterType', 'month', 'year', 'startDate', 'endDate'
        ));
    }


    public function exportExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['Report', 'Amount'],
            ['Total Sales', OrderDetails::sum('total')],
            ['COGS', PurchaseDetails::sum(DB::raw('quantity * unitcost'))],
            ['Profit', OrderDetails::sum('total') - PurchaseDetails::sum(DB::raw('quantity * unitcost'))],
        ]);

        $writer = new Xls($spreadsheet);
        $filename = 'profit-loss-' . date('Ymd-His') . '.xls';

        if (ob_get_length()) { ob_end_clean(); }

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer->save('php://output');
    }

    public function exportPDF()
    {
        $data = [
            'sales' => OrderDetails::sum('total'),
            'cogs' => PurchaseDetails::sum(DB::raw('quantity * unitcost')),
        ];
        $data['profit'] = $data['sales'] - $data['cogs'];

        $pdf = Pdf::loadView('reports.pl-pdf', $data);
        return $pdf->download('profit-loss.pdf');
    }
}
