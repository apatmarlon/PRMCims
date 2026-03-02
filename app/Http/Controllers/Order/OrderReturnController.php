<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\OrderReturnDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderReturnController extends Controller
{
    public function index()
    {
      return view('orders.return_index');
    }

    // Show single return
    public function show(OrderReturn $return)
    {
        $return->load([
            'customer',
            'order',
            'details.product'
        ]);

        return view('orders.return_show', compact('return'));
    }
    public function create(Order $order)
    {
        $order->load('details.product');

        return view('orders.return', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        $request->validate([
            'products' => 'required|array',
            'reason'   => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $order) {

            $order->load('details');

            $return = OrderReturn::create([
                'order_id'     => $order->id,
                'customer_id'  => $order->customer_id,
                'total_refund' => 0,
                'reason'       => $request->reason,
                'processed_by' => auth()->user()->name,
            ]);

            $totalRefund = 0;

            foreach ($request->products as $productId => $qty) {

            if ($qty <= 0) continue;

            $detail = $order->details
                ->where('product_id', $productId)
                ->first();

            if (! $detail) {
                throw new \Exception('Invalid product in return.');
            }

            // 🛑 HARD STOP: prevent over-return
            if ($qty > $detail->remainingQty()) {
                throw new \Exception(
                    "Return quantity exceeds remaining quantity for {$detail->product->name}"
                );
            }

            $lineTotal = $detail->unitcost * $qty;

            OrderReturnDetail::create([
                'order_return_id' => $return->id,
                'product_id'      => $productId,
                'quantity'        => $qty,
                'unit_price'      => $detail->unitcost,
                'total'           => $lineTotal,
            ]);

            // ⬆ restore stock
            Product::where('id', $productId)
                ->increment('quantity', $qty);

            // 🧮 track returned qty
            $detail->increment('returned_quantity', $qty);

            $totalRefund += $lineTotal;
        }

            // Update totals
            $return->update([
                'total_refund' => $totalRefund,
            ]);

            // Optional: adjust order totals
            $order->update([
                'total' => max(0, $order->total - $totalRefund),
                'due'   => max(0, $order->due - $totalRefund),
            ]);
        });

        return redirect()
            ->route('orders.index', $order)
            ->with('success', 'Order return processed successfully.');
    }
    public function print(OrderReturn $return)
    {
        $return->load([
            'customer',
            'order',
            'details.product'
        ]);

        $pdf = Pdf::loadView(
            'orders.return_print',
            compact('return')
        )->setPaper('a4', 'portrait');

        return $pdf->stream(
            'order-return-' . $return->id . '.pdf'
        );
    }
}
