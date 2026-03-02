<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Order\DueOrderController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Purchase\PurchaseController;
use App\Http\Controllers\Order\OrderPendingController;
use App\Http\Controllers\Order\OrderCompleteController;
use App\Http\Controllers\Quotation\QuotationController;
use App\Http\Controllers\Dashboards\DashboardController;
use App\Http\Controllers\Product\ProductExportController;
use App\Http\Controllers\Product\ProductImportController;
use App\Http\Controllers\Reports\ProfitLossController;
use App\Http\Controllers\Reports\PurchaseSaleReportController;
use App\Http\Controllers\Reports\StockReportController;
use App\Http\Controllers\Reports\MarkupReportController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\Reports\ProductStockReportController;
use App\Http\Controllers\Reports\ProductOrderReportController;
use App\Http\Controllers\Order\OrderReturnController;
use App\Http\Controllers\Reports\SalesvsReturnController;
use App\Http\Controllers\Reports\GrossProfitReportController;
use App\Http\Controllers\Reports\CustomerStatementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('php/', function () {
    return phpinfo();
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::resource('/users', UserController::class); //->except(['show']);
    Route::put('/user/change-password/{username}', [UserController::class, 'updatePassword'])->name('users.updatePassword');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('/quotations', QuotationController::class);
    Route::resource('/customers', CustomerController::class);
    Route::resource('/suppliers', SupplierController::class);
    Route::resource('/categories', CategoryController::class);
    Route::resource('/units', UnitController::class);
    Route::resource('/brands', BrandController::class);

    // Route Products
    Route::get('/products/import', [ProductImportController::class, 'create'])->name('products.import.view');
    Route::post('/products/import', [ProductImportController::class, 'store'])->name('products.import.store');
    Route::get('/products/export', [ProductExportController::class, 'create'])->name('products.export.store');
    Route::resource('/products', ProductController::class);
    Route::get('products/{product}/history', [ProductController::class, 'history'])->name('products.history');


    // Route Orders
    Route::post('/invoice/create', [InvoiceController::class, 'create'])->name('invoice.create');
  

    Route::get('/orders/returns', [OrderReturnController::class, 'index'])
            ->name('orders.return.index');

    Route::get('/orders/returns/{return}', [OrderReturnController::class, 'show'])
            ->name('orders.return.show');

    Route::get('/orders/{order}/return', [OrderReturnController::class, 'create'])
            ->name('orders.return.create');

    Route::post('/orders/{order}/return', [OrderReturnController::class, 'store'])
            ->name('orders.return.store');
    Route::get('/orders/returns/{return}/print', [OrderReturnController::class, 'print'])
            ->name('orders.return.print');
    Route::get('reports/sales-vs-returns/pdf',
        [SalesvsReturnController::class, 'downloadPdf'])
        ->name('reports.salesvsreturns.pdf');
    Route::get('reports/sales-vs-returns/excel',
        [SalesvsReturnController::class, 'downloadExcel'])
        ->name('reports.salesvsreturns.excel');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/pending', OrderPendingController::class)->name('orders.pending');
    Route::get('/orders/complete', OrderCompleteController::class)->name('orders.complete');

    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->name('orders.show');
    Route::put('/orders/update/{order}', [OrderController::class, 'update'])->name('orders.update');
    // DUES
    Route::get('/due/orders/', [DueOrderController::class, 'index'])->name('due.index');
    Route::get('/due/order/view/{order}', [DueOrderController::class, 'show'])->name('due.show');
    Route::get('/due/order/edit/{order}', [DueOrderController::class, 'edit'])->name('due.edit');
    Route::put('/due/order/update/{order}', [DueOrderController::class, 'update'])->name('due.update');

    // TODO: Remove from OrderController
    Route::get('/orders/details/{order_id}/download', [OrderController::class, 'downloadInvoice'])->name('order.downloadInvoice');

    // Route Purchases
    Route::get('/purchases/approved', [PurchaseController::class, 'approvedPurchases'])->name('purchases.approvedPurchases');
    Route::get('/purchases/report', [PurchaseController::class, 'dailyPurchaseReport'])->name('purchases.dailyPurchaseReport');
    Route::get('/purchases/report/export', [PurchaseController::class, 'getPurchaseReport'])->name('purchases.getPurchaseReport');
    Route::post('/purchases/report/export', [PurchaseController::class, 'exportPurchaseReport'])->name('purchases.exportPurchaseReport');
    Route::get('purchases/{purchase}/print', [PurchaseController::class, 'print'])
        ->name('purchases.print');
    Route::get('/purchases/export', [App\Http\Controllers\Purchase\PurchaseController::class, 'exportPurchases'])
        ->name('purchases.export');
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/list', [PurchaseController::class, 'list'])->name('purchases.list');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');

    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/purchases/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('/purchases/{purchase}', [PurchaseController::class, 'update'])->name('purchases.update');
    
    Route::put('/purchases/{purchase}/edit', [PurchaseController::class, 'received'])->name('purchases.received');
     Route::put('/purchases/{purchase}/paid', [PurchaseController::class, 'paid'])->name('purchases.paid');
    Route::delete('/purchases/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.delete');

    //Reports Routes
    Route::prefix('reports')->group(function () {
    Route::get('/profit-loss', [ProfitLossController::class, 'index'])->name('reports.pl.index');
    Route::get('/profit-loss/excel', [ProfitLossController::class, 'exportExcel'])->name('reports.pl.excel');
    Route::get('/profit-loss/pdf', [ProfitLossController::class, 'exportPDF'])->name('reports.pl.pdf'); 
    Route::get('purchase-sale', [PurchaseSaleReportController::class, 'index'])->name('reports.purchase-sale');
    Route::post('purchase-sale/export-excel', [PurchaseSaleReportController::class, 'exportExcel'])->name('reports.purchase-sale.excel');
    Route::post('purchase-sale/export-pdf', [PurchaseSaleReportController::class, 'exportPdf'])->name('reports.purchase-sale.pdf');
    Route::post('purchase-sale/export-excel-combined', [PurchaseSaleReportController::class, 'exportExcelCombined'])
        ->name('reports.purchase-sale.excel-combined');
    Route::post('purchase-sale/export-pdf-combined', [PurchaseSaleReportController::class, 'exportPdfCombined'])
        ->name('reports.purchase-sale.pdf-combined');
    
        // Supplier & Customer Report
    Route::get('/reports/supplier-customer', [\App\Http\Controllers\Reports\SupplierCustomerReportController::class, 'index'])
        ->name('reports.supplier-customer');
    Route::post('/reports/supplier-customer/excel', [\App\Http\Controllers\Reports\SupplierCustomerReportController::class, 'exportExcel'])
        ->name('reports.supplier-customer.excel');
    Route::post('/reports/supplier-customer/pdf', [\App\Http\Controllers\Reports\SupplierCustomerReportController::class, 'exportPDF'])
        ->name('reports.supplier-customer.pdf');
        });
    
    // Stock Report 
    Route::get('/stock', [StockReportController::class, 'index'])->name('stock.index');
    Route::post('/stock/excel', [StockReportController::class, 'exportExcel'])->name('reports.stock.excel');
    Route::post('/stock/pdf', [StockReportController::class, 'exportPDF'])->name('reports.stock.pdf');

    // Markup Report
    Route::prefix('reports')->group(function() {
    Route::get('markup', [MarkupReportController::class, 'index'])->name('reports.markup');
    Route::get('markup/excel', [MarkupReportController::class, 'exportExcel'])->name('reports.markup.excel');
    Route::get('markup/pdf', [MarkupReportController::class, 'exportPDF'])->name('reports.markup.pdf');
    Route::get('/reports/sales-vs-returns', [SalesvsReturnController::class, 'index'])
    ->name('reports.sales-vs-returns');
    });

    // Product Report
    Route::get('/reports/product-stock', [ProductStockReportController::class, 'index'])
    ->name('reports.product-stock');
    Route::get('/reports/product-stock/pdf', [ProductStockReportController::class, 'exportPDF'])
    ->name('reports.product-stock.pdf');
    Route::get('reports/product-stock/excel', [ProductStockReportController::class, 'exportExcel'])
     ->name('reports.product-stock.excel');
    Route::get('reports/products/search', [ProductStockReportController::class, 'search'])->name('reports.products.search');
    
    Route::get('product-orders', [ProductOrderReportController::class, 'index'])
            ->name('reports.product-orders');

    Route::get('product-orders/pdf', [ProductOrderReportController::class, 'pdf'])
            ->name('reports.product-orders.pdf');

    Route::get('product-orders/excel', [ProductOrderReportController::class, 'excel'])
            ->name('reports.product-orders.excel');
    Route::get('reports/products/search', [ProductOrderReportController::class, 'search'] 
            )->name('reports.products.search');
    Route::get('/gross-profit', [GrossProfitReportController::class, 'index'])
        ->name('reports.gross_profit');

    // Gross Profit Report - Excel export
    Route::get('/gross-profit/excel', [GrossProfitReportController::class, 'exportExcel'])
        ->name('reports.gross_profit.excel');

    // Gross Profit Report - PDF export
    Route::get('/gross-profit/pdf', [GrossProfitReportController::class, 'exportPDF'])
        ->name('reports.gross_profit.pdf');
        
    Route::prefix('reports/soa')->name('soa.')->middleware('auth')->group(function () {
    // Customer SOA list & view
        Route::get('/', [CustomerStatementController::class, 'index'])->name('index');
        Route::get('/{customer}', [CustomerStatementController::class, 'show'])->name('show');

        // Transaction CRUD
        Route::get('/{customer}/transaction/create', [CustomerStatementController::class, 'create'])->name('create');
        Route::post('/{customer}/transaction', [CustomerStatementController::class, 'store'])->name('store');

        Route::get('/transaction/{transaction}/edit', [CustomerStatementController::class, 'edit'])->name('edit');
        Route::put('/transaction/{transaction}', [CustomerStatementController::class, 'update'])->name('update');
        Route::delete('/transaction/{transaction}', [CustomerStatementController::class, 'destroy'])->name('destroy');
        Route::get('/{customer}/pdf', [CustomerStatementController::class, 'pdf'])
            ->name('pdf');

        Route::get('/{customer}/excel', [CustomerStatementController::class, 'excel'])
            ->name('excel');
        Route::get('/customers/{customer}/orders/search',[CustomerStatementController::class, 'searchOrders'])
            ->name('orders.search');
    });

    });
    

require __DIR__.'/auth.php';

Route::get('test/', function (){
//    return view('test');
    return view('orders.create');
});
