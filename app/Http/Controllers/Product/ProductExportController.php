<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class ProductExportController extends Controller
{
    public function create()
    {
        // Eager load relationships to avoid N+1 query problem
        $products = Product::with(['category', 'unit', 'brand', 'supplier'])
            ->orderBy('name')
            ->get();

        $product_array[] = array(
            'Product Name',
            'Category Name',
            'Unit Name',
            'Brand Name',
            'Supplier Name',
            'Product Code',
            'Stock',
            'Buying Price',
            'Selling Price',
            'Product Image',
        );

        foreach ($products as $product) {
            $product_array[] = array(
                'Product Name'  => $product->name,
                'Category Name' => optional($product->category)->name,
                'Unit Name'     => optional($product->unit)->name,
                'Brand Name'    => optional($product->brand)->name,
                'Supplier Name' => optional($product->supplier)->name,
                'Product Code'  => $product->code,
                'Stock'         => $product->quantity,
                'Buying Price'  => $product->buying_price,
                'Selling Price' => $product->selling_price,
                'Product Image' => $product->product_image,
            );
        }

        $this->store($product_array);
    }
    public function store($products)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($products);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="products.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }
}
