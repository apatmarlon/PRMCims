<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerStatementAccount;
use App\Models\CustomerStatementTransaction;
use Illuminate\Http\Request;
use DB;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Models\Order;

class CustomerStatementController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1. CUSTOMER LIST
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $customers = Customer::with('statementAccount')->get();

        return view('reports.soa.index', compact('customers'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. SHOW SOA LEDGER
    |--------------------------------------------------------------------------
    */
    public function show(Customer $customer)
{
    $soa = $customer->statementAccount()
        ->with(['transactions' => function ($q) {
            $q->orderBy('transaction_date')
              ->orderBy('id');
        }])
        ->firstOrFail();

    return view('reports.soa.show', compact('customer', 'soa'));
}

    /*
    |--------------------------------------------------------------------------
    | 3. CREATE TRANSACTION FORM
    |--------------------------------------------------------------------------
    */
    public function create(Customer $customer)
    {
        return view('reports.soa.create', compact('customer'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. STORE NEW TRANSACTION + RECALCULATE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request, Customer $customer)
{
    DB::transaction(function () use ($request, $customer) {
        $request->validate([
            'transaction_date' => ['required', 'date'],
            'description'      => ['required', 'string'],
            'debit'            => ['nullable', 'numeric', 'min:0'],
            'credit'           => ['nullable', 'numeric', 'min:0'],
        ]);
        $debit  = $request->input('debit', 0);
        $credit = $request->input('credit', 0);

        // ❗ Accounting rule enforcement
        if (($debit > 0 && $credit > 0) || ($debit == 0 && $credit == 0)) {
            return back()
                ->withInput()
                ->with('error', 'Please enter a value in either Debit OR Credit only.');
        }
        // Ensure the customer has a statement account
        $soa = $customer->statementAccount ?? $customer->statementAccount()->create([
            'beginning_balance' => 0,
            'start_date' => now(),
            'end_date' => null,
        ]);

        // Create the transaction
        $txn = $soa->transactions()->create($request->only([
            'transaction_date',
            'description',
            'due_date',  
            'debit',
            'credit',
            'ref_no'
        ]));

        // Recalculate balances
        $this->recalculateBalance($soa);
    });

    return redirect()->route('soa.show', $customer)->with('success', 'Transaction added.');
}
    public function edit(CustomerStatementTransaction $transaction)
    {
        return view('reports.soa.edit', compact('transaction'));
    }

    /*
    |--------------------------------------------------------------------------
    | 6. UPDATE TRANSACTION + RECALCULATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, CustomerStatementTransaction $transaction)
    {
        DB::transaction(function () use ($request, $transaction) {

            $transaction->update($request->only([
                'transaction_date',
                'description',
                'due_date',  
                'debit',
                'credit',
                'ref_no'
            ]));

            $this->recalculateBalance($transaction->statementAccount);
        });

        return redirect()->route('soa.show', $transaction->statementAccount->customer)->with('success', 'Transaction updated.');
    }

    /*
    |--------------------------------------------------------------------------
    | 7. DELETE TRANSACTION + RECALCULATE
    |--------------------------------------------------------------------------
    */
    public function destroy(CustomerStatementTransaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            $soa = $transaction->statementAccount;
            $transaction->delete();

            $this->recalculateBalance($soa);
        });

        return redirect()->back()->with('success', 'Transaction deleted.');
    }

    /*
    |--------------------------------------------------------------------------
    | 8. BALANCE RECALCULATION (CORE LOGIC)
    |--------------------------------------------------------------------------
    */
   private function recalculateBalance(CustomerStatementAccount $soa)
{
    $balance = $soa->beginning_balance;

    $transactions = $soa->transactions()
        ->orderBy('transaction_date')
        ->orderBy('id') // 👈 critical
        ->get();

    foreach ($transactions as $txn) {
        $balance = $balance + $txn->debit - $txn->credit;

        $txn->update([
            'balance' => $balance
        ]);
    }
}
    public function pdf(Customer $customer)
    {
        $soa = $customer->statementAccount()
            ->with('transactions')
            ->firstOrFail();

        $totals = $this->calculateSoaTotals($soa);

        $pdf = Pdf::loadView('reports.soa.pdf', compact('customer', 'soa', 'totals'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('SOA-' . $customer->name . '.pdf');
    }
    public function excel(Customer $customer)
    {
        $soa = $customer->statementAccount()
            ->with('transactions')
            ->firstOrFail();
        $totals = $this->calculateSoaTotals($soa);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Statement of Account');
        $sheet->setCellValue('A2', 'Customer: ' . $customer->name);

        // Table headers
        $sheet->fromArray([
            ['Date', 'Ref #', 'Description', 'Due Date', 'Debit', 'Credit', 'Balance']
        ], null, 'A4');

        $row = 5;
        $balance = $soa->beginning_balance;

        // Beginning Balance row
        $sheet->fromArray([
            '', '', 'Beginning Balance', '', '', $balance
        ], null, 'A' . $row++);

        foreach ($soa->transactions as $txn) {
            $sheet->fromArray([
                $txn->transaction_date,
                $txn->ref_no,
                $txn->description,
                $txn->due_date ?? '',
                $txn->debit,
                $txn->credit,
                $txn->balance,
            ], null, 'A' . $row++);
        }
        $row += 2;

        $sheet->setCellValue("E$row", 'TOTAL RECEIVABLE');
        $sheet->setCellValue("F$row", $totals['totalReceivable']);

        $row++;
        $sheet->setCellValue("E$row", 'LESS TOTAL PAYMENT');
        $sheet->setCellValue("F$row", $totals['totalPayment']);

        $row++;
        $sheet->setCellValue("E$row", 'TOTAL AMOUNT DUE');
        $sheet->setCellValue("F$row", $totals['totalAmountDue']);

        $row++;
        $sheet->setCellValue("E$row", 'TOTAL AMOUNT OVERDUE');
        $sheet->setCellValue("F$row", $totals['totalOverdue']);

        $row++;
        $sheet->setCellValue("E$row", 'TOTAL AMOUNT BEFORE DUE DATE');
        $sheet->setCellValue("F$row", $totals['totalBeforeDueDate']);
        // Number formatting
        $sheet->getStyle('D:E')->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        $sheet->getStyle("E" . ($row - 4) . ":F$row")->getFont()->setBold(true);

        $sheet->getStyle("F" . ($row - 4) . ":F$row")
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
        $sheet->getStyle('F')->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'SOA-' . $customer->name . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);

    }
    private function calculateSoaTotals($soa)
    {
        $totalReceivable = $soa->transactions->sum('debit');
        $totalPayment    = $soa->transactions->sum('credit');

        $totalAmountDue = $totalReceivable - $totalPayment;

        $today = now()->toDateString();

        $totalOverdue = $soa->transactions
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->sum(function ($t) {
                return $t->debit - $t->credit;
            });

        $totalBeforeDueDate = $totalAmountDue - $totalOverdue;

        return compact(
            'totalReceivable',
            'totalPayment',
            'totalAmountDue',
            'totalOverdue',
            'totalBeforeDueDate'
        );
    }
    public function searchOrders(Customer $customer, Request $request)
    {
        $q = $request->get('q');

        return Order::where('customer_id', $customer->id)
            ->where('invoice_no', 'like', "%{$q}%")
            ->get(['id', 'invoice_no', 'total']);
    }


}
