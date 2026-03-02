<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Product::with('category')->get()->map(function ($p) {
            return [
                'Name' => $p->name,
                'Code' => $p->code,
                'Category' => $p->category->name ?? '-',
                'Quantity' => $p->quantity,
                'Alert Qty' => $p->quantity_alert,
            ];
        });
    }

    public function headings(): array
    {
        return ['Name', 'Code', 'Category', 'Quantity', 'Alert Qty'];
    }
}
