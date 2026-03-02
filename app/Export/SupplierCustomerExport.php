<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class SupplierCustomerExport implements FromView
{
    public function view(): View
    {
        return view('reports.excel.supplier-customer', [
            'customers' => Customer::all(),
            'suppliers' => Supplier::all(),
        ]);
    }
}
