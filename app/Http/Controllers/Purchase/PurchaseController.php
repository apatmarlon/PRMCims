<?php

namespace App\Http\Controllers\Purchase;

use App\Enums\PurchaseStatus;
use App\Enums\PaymentStatus;
use App\Enums\PurchaseStat;
use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\StorePurchaseRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;



class PurchaseController extends Controller
{
    public function index()
    {
        return view('purchases.index', [
            'purchases' => Purchase::latest()->get(),
        ]);
    }
     public function list()
    {
        return view('purchases.list', [
            'purchases' => Purchase::with('createdBy')->latest()->get(),
        ]);
    }

    public function approvedPurchases()
    {
        $purchases = Purchase::with(['supplier'])
            ->where('status', PurchaseStatus::COMPLETED)->get(); // 1 = approved

        return view('purchases.approved-purchases', [
            'purchases' => $purchases,
        ]);
    }

    public function show(Purchase $purchase)
    {
        // Load supplier and details with the related product
        $purchase->load(['supplier', 'details.product', 'createdBy', 'updatedBy']);

        // All products related to this purchase
        $products = $purchase->details;

        return view('purchases.details-purchase', [
            'purchase' => $purchase,
            'products' => $products
        ]);
    }

    public function edit(Purchase $purchase)
    {
        // N+1 Problem if load 'createdBy', 'updatedBy',
        $purchase->with(['supplier', 'details'])->get();

        return view('purchases.edit', [
            'purchase' => $purchase,
        ]);
    }

    public function create()
    {
        return view('purchases.create', [
            'categories' => Category::select(['id', 'name'])->get(),
            'suppliers' => Supplier::select(['id', 'name'])->get(),
        ]);
    }

    public function store(StorePurchaseRequest $request)
    {
        // Create Purchase with discount fields
        $purchase = Purchase::create([
            'supplier_id'         => $request->supplier_id,
            'date'                => $request->date,
            'purchase_no'         => $request->purchase_no,
            'discount_percentage' => $request->discount_percentage ?? 0,
            'discount_amount'     => $request->discount_amount ?? 0,
            'payterm'             => $request->payterm ?? 0,
            'payment_status'      => $request->payment_status ?? 1,
            'total_amount'        => $request->total_amount ?? 0,
            'created_by'          => auth()->id(),
        ]);

        // Save Purchase Details
        if (!empty($request->invoiceProducts)) {
            foreach ($request->invoiceProducts as $product) {
               $purchase->details()->create([
            'product_id'           => $product['product_id'],
            'quantity'             => $product['quantity'],
            'unitcost'             => $product['is_freebie'] ? 0 : floatval(str_replace(',', '', $product['unitcost'])),
            'discount_percentage'  => $product['is_freebie'] ? 0 : ($product['discount_percentage'] ?? 0),
            'discount_amount'      => $product['is_freebie'] ? 0 : ($product['discount_amount'] ?? 0),
            'total'                => $product['is_freebie'] ? 0 : floatval(str_replace(',', '', $product['total'])),
            'is_freebie'           => $product['is_freebie'] ?? 0,
        ]);

            }
        }

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase has been created!');
    }

    public function update(Purchase $purchase, Request $request)
    {
        DB::transaction(function () use ($purchase) {
        // Fetch all products in this purchase
       

        // Mark purchase as approved
        $purchase->update([
            'status' => PurchaseStatus::COMPLETED,
            'updated_by' => auth()->id(),
        ]);
    });

    return redirect()
        ->route('purchases.index')
        ->with('success', 'Purchase approved and stock updated!');
    }
    public function received(Purchase $purchase, Request $request)
    {
        DB::transaction(function () use ($purchase) {
        // Fetch all products in this purchase
        $products = PurchaseDetails::where('purchase_id', $purchase->id)->get();

        // Loop through products and update inventory
        foreach ($products as $product) {
            Product::where('id', $product->product_id)
                ->update([
                    'quantity' => DB::raw('quantity + ' . $product->quantity)
                ]);
        }

        // Mark purchase as approved
        $purchase->update([
            'purchase_status' => PurchaseStat::RECEIVED,
            'updated_by' => auth()->id(),
        ]);
    });

    return redirect()
        ->route('purchases.list')
        ->with('success', 'Purchase approved and stock updated!');
    }
    public function paid(Purchase $purchase, Request $request)
    {
        DB::transaction(function () use ($purchase) {
        // Fetch all products in this purchase
        

        // Mark purchase as approved
        $purchase->update([
            'payment_status' => PaymentStatus::PAID,
            'updated_by' => auth()->id(),
        ]);
    });

    return redirect()
        ->route('purchases.list')
        ->with('success', 'Purchase approved and stock updated!');
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase has been deleted!');
    }

    public function dailyPurchaseReport()
    {
        $purchases = Purchase::with(['supplier'])
            //->where('purchase_status', 1)
            ->where('date', today()->format('Y-m-d'))->get();

        return view('purchases.daily-report', [
            'purchases' => $purchases,
        ]);
    }

    public function getPurchaseReport()
    {
        return view('purchases.report-purchase');
    }

    public function exportPurchaseReport(Request $request)
    {
        $rules = [
            'start_date' => 'required|string|date_format:Y-m-d',
            'end_date' => 'required|string|date_format:Y-m-d',
        ];

        $validatedData = $request->validate($rules);

        $sDate = $validatedData['start_date'];
        $eDate = $validatedData['end_date'];

        $purchases = DB::table('purchase_details')
            ->join('products', 'purchase_details.product_id', '=', 'products.id')
            ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
            ->join('users', 'users.id', '=', 'purchases.created_by')
            ->whereBetween('purchases.purchase_date', [$sDate, $eDate])
            ->where('purchases.purchase_status', '1')
            ->select('purchases.purchase_no', 'purchases.purchase_date', 'purchases.supplier_id', 'products.code', 'products.name', 'purchase_details.quantity', 'purchase_details.unitcost', 'purchase_details.total', 'users.name as created_by')
            ->get();

        dd($purchases);

        $purchase_array[] = [
            'Date',
            'No Purchase',
            'Supplier',
            'Product Code',
            'Product',
            'Quantity',
            'Unitcost',
            'Total',
            'Created By'
        ];

        foreach ($purchases as $purchase) {
            $purchase_array[] = [
                'Date' => $purchase->purchase_date,
                'No Purchase' => $purchase->purchase_no,
                'Supplier' => $purchase->supplier_id,
                'Product Code' => $purchase->product_code,
                'Product' => $purchase->product_name,
                'Quantity' => $purchase->quantity,
                'Unitcost' => $purchase->unitcost,
                'Total' => $purchase->total,
            ];
        }

        $this->exportExcel($purchase_array);
    }

    public function exportExcel($products)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($products);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="purchase-report.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit(); 
        } catch (Exception $e) {
            return $e;
        }
    }
    public function print(Purchase $purchase)
        {
            $purchase->load(['supplier', 'details.product', 'createdBy', 'updatedBy']);
            $products = $purchase->details;

            return view('purchases.print', compact('purchase', 'products'));
        }
        public function exportPurchases()
        {
            $purchases = Purchase::with('supplier')
                ->orderBy('id', 'DESC')
                ->get();

            // Excel Header
            $excelData[] = [
                'Purchase No',
                'Supplier',
                'Date',
                'Total Amount',
                'Status'
            ];

            // Excel Rows
            foreach ($purchases as $p) {
                $excelData[] = [
                    $p->purchase_no,
                    $p->supplier->name ?? 'N/A',
                    Carbon::parse($p->date)->format('Y-m-d'),
                    number_format($p->total_amount, 2),
                    $p->status == \App\Enums\PurchaseStatus::COMPLETED ? 'COMPLETED' : 'PENDING',
                ];
            }

            // Create Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray($excelData, null, 'A1');

            // Auto Column Width
            foreach (range('A', 'E') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Output
            $writer = new Xls($spreadsheet);

            $filename = 'purchase-list-' . date('Ymd-His') . '.xls';

            // Clean buffer to avoid corrupt file
            if (ob_get_length()) {
                ob_end_clean();
            }

            // Download Headers
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        }
}
