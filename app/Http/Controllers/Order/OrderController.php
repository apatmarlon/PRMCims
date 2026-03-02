<?php

namespace App\Http\Controllers\Order;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\CustomerSOAService;


class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('customer')->latest()->get();

        return view('orders.index', [
            'orders' => $orders,
        ]);
    }

    public function create()
    {
        Cart::instance('order')
            ->destroy();

        return view('orders.create', [
            'carts' => Cart::content(),
            'customers' => Customer::all(['id', 'name']),
            'products' => Product::with(['category', 'unit'])->get(),
        ]);
    }

   public function store(OrderStoreRequest $request)
    {
      
        $order = Order::create([
            ...$request->all(),
            'note'     => $request->note,
            'order_date'  => $request->order_date,
            'added_by' => auth()->user()->name,
            'total'    => 0, 
        ]);

     
        $contents = Cart::instance('order')->content();

        foreach ($contents as $content) {
            OrderDetails::create([
                'order_id'     => $order->id,
                'product_id'   => $content->id,
                'quantity'     => $content->qty,
                'unitcost'     => $content->options->unitcost,
                'markup_price' => $content->options->markup,
                'total'        =>
                    ($content->options->unitcost + $content->options->markup)
                    * $content->qty,
                'order_date' => $request->order_date,
            ]);
        }

       
        $orderTotal = OrderDetails::where('order_id', $order->id)
            ->sum('total');
        $orderSubTotal = OrderDetails::where('order_id', $order->id)
            ->sum('unitcost');
      
        $order->update([
            'sub_total' => $orderSubTotal,
            'total'     => $orderTotal,
            'due'       => $orderTotal - $request->pay,
        ]);

       /*
            |--------------------------------------------------------------------------
            | CUSTOMER STATEMENT OF ACCOUNT (SOA)
            |--------------------------------------------------------------------------
            */
            $soa = CustomerSOAService::getOrCreate($order->customer_id);

            // Get last running balance
            $lastBalance = $soa->transactions()
                ->latest()
                ->value('balance') ?? $soa->beginning_balance;

            /*
            |--------------------------------------------------------------------------
            | 1. RECORD PAYMENT (CREDIT)
            |--------------------------------------------------------------------------
            */
            if ($request->pay > 0) {

                $newBalance = $lastBalance - $request->pay;

                $soa->transactions()->create([
                    'transaction_date' => $order->order_date,
                    'ref_no'           => 'OR-' . $order->id,
                    'description'      => 'Payment Received',
                    'debit'            => 0,
                    'credit'           => $request->pay,
                    'balance'          => $newBalance,
                ]);

                $lastBalance = $newBalance;
            }

            /*
            |--------------------------------------------------------------------------
            | 2. RECORD UNPAID / CREDIT BALANCE (DEBIT)
            |--------------------------------------------------------------------------
            */
            if ($order->due > 0) {

                $newBalance = $lastBalance + $order->due;

                $soa->transactions()->create([
                    'transaction_date' => $order->order_date,
                    'ref_no'           => $order->invoice_no,
                    'due_date'         => Carbon::parse($order->order_date)->addDays(30),
                    'description'      => 'Sales Invoice',
                    'debit'            => $order->due,
                    'credit'           => 0,
                    'balance'          => $newBalance,
                ]);
            }


        Cart::destroy();

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order has been created!');
    }

    public function show(Order $order)
    {
        $order->loadMissing(['customer', 'details'])->get();

        return view('orders.show', [
            'order' => $order,
        ]);
    }

    public function update(Order $order, Request $request)
    {
        // TODO refactoring

        // Reduce the stock of each product in the order
        $products = OrderDetails::where('order_id', $order->id)->get();

        foreach ($products as $product) {
            Product::where('id', $product->product_id)
                ->update(['quantity' => DB::raw('quantity - ' . $product->quantity)]);
        }

        // Update the order status to complete
            $order->update([
                'order_status' => OrderStatus::COMPLETE,
        ]);

        return redirect()
            ->route('orders.complete')
            ->with('success', 'Order has been completed and stock updated!');
    }

    public function destroy(Order $order)
    {
        $order->delete();
    }

    public function downloadInvoice($order)
    {
        $order = Order::with(['customer', 'details'])
            ->where('id', $order)
            ->first();

        return view('orders.print-invoice', [
            'order' => $order,
        ]);
    }
}
