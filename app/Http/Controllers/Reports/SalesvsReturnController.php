<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesvsReturnController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $customerId = $request->input('customer'); // CHANGED

        $query = Order::with([
            'customer',
            'details.product.brand'
        ]);

        $query->where('order_status', \App\Enums\OrderStatus::COMPLETE);

        if ($from) {
            $query->whereDate('order_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('order_date', '<=', $to);
        }

        // FILTER BY CUSTOMER
        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        $orders = $query->get();

        $report = [];

        foreach ($orders as $order) {
            foreach ($order->details as $detail) {

                $productId = $detail->product_id;

                if (!isset($report[$productId])) {
                    $report[$productId] = [
                        'product' => $detail->product->name,
                        'brand' => $detail->product->brand?->name,
                        'customer' => $order->customer?->name,
                        'sold_qty' => 0,
                        'returned_qty' => 0,
                        'sales' => 0,
                        'returns' => 0,
                    ];
                }

                $report[$productId]['sold_qty'] += $detail->quantity;
                $report[$productId]['returned_qty'] += $detail->returned_quantity;
                $report[$productId]['sales'] += $detail->quantity * $detail->unitcost;
                $report[$productId]['returns'] += $detail->returned_quantity * $detail->unitcost;
            }
        }

        $customers = Customer::orderBy('name')->get();

        return view(
            'reports.sales_vs_returns',
            compact('report', 'from', 'to', 'customers', 'customerId')
        );
    }
    public function downloadPdf(Request $request)
    {
        $data = $this->generateReport($request);

        $pdf = Pdf::loadView('reports.sales_vs_returns_pdf', $data);
        return $pdf->download('sales_vs_returns.pdf');
    }
    public function downloadExcel(Request $request)
    {
        // reuse your report logic
        $data = $this->generateReport($request);
        $report = $data['report'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // HEADER ROW
        $sheet->setCellValue('A1', 'Product');
        $sheet->setCellValue('B1', 'Brand');
        $sheet->setCellValue('C1', 'Customer');
        $sheet->setCellValue('D1', 'Sold Qty');
        $sheet->setCellValue('E1', 'Returned Qty');
        $sheet->setCellValue('F1', 'Sales');
        $sheet->setCellValue('G1', 'Returns');
        $sheet->setCellValue('H1', 'Net Sales');

        $row = 2;

        foreach ($report as $item) {
            $sheet->setCellValue('A'.$row, $item['product']);
            $sheet->setCellValue('B'.$row, $item['brand']);
            $sheet->setCellValue('C'.$row, $item['customer'] ?? '-');
            $sheet->setCellValue('D'.$row, $item['sold_qty']);
            $sheet->setCellValue('E'.$row, $item['returned_qty']);
            $sheet->setCellValue('F'.$row, $item['sales']);
            $sheet->setCellValue('G'.$row, $item['returns']);
            $sheet->setCellValue('H'.$row, $item['sales'] - $item['returns']);
            $row++;
        }

        $writer = new Xls($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'sales_vs_returns.xls';

        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename.'"');
        $response->headers->set('Cache-Control','max-age=0');

        return $response;
    }
    private function generateReport(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $customerId = $request->customer;

        $query = Order::with(['customer','details.product.brand'])
            ->where('order_status', \App\Enums\OrderStatus::COMPLETE);

        if ($from) $query->whereDate('order_date', '>=', $from);
        if ($to) $query->whereDate('order_date', '<=', $to);
        if ($customerId) $query->where('customer_id', $customerId);

        $orders = $query->get();

        $report = [];

        foreach ($orders as $order) {
            foreach ($order->details as $detail) {

                $pid = $detail->product_id;

                if (!isset($report[$pid])) {
                    $report[$pid] = [
                        'product' => $detail->product->name,
                        'brand' => $detail->product->brand?->name,
                        'customer' => $order->customer?->name,
                        'sold_qty' => 0,
                        'returned_qty' => 0,
                        'sales' => 0,
                        'returns' => 0,
                    ];
                }

                $report[$pid]['sold_qty'] += $detail->quantity;
                $report[$pid]['returned_qty'] += $detail->returned_quantity;
                $report[$pid]['sales'] += $detail->quantity * $detail->unitcost;
                $report[$pid]['returns'] += $detail->returned_quantity * $detail->unitcost;
            }
        }

        return compact('report','from','to');
    }

}
