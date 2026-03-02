<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class ProductHistoryService
{
    protected Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Get full product history with running stock.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getHistory(): Collection
    {
        // Load all related models
        $this->product->load([
            'purchases.purchase',
            'sales.order',
            'returns.orderReturn',
        ]);

        $history = collect();

        // Purchases
        foreach ($this->product->purchases as $pd) {
            $history->push([
                'type' => 'purchase',
                'date' => $pd->purchase->date,
                'quantity' => $pd->quantity,
                'unit_price' => $pd->unitcost,
                'total' => $pd->total,
                'reference' => $pd->purchase->purchase_no,
            ]);
        }

        // Sales
        foreach ($this->product->sales as $od) {
            $history->push([
                'type' => 'sale',
                'date' => $od->order->order_date,
                'quantity' => -$od->quantity, // outgoing stock
                'unit_price' => $od->unitcost,
                'total' => $od->total,
                'reference' => $od->order->invoice_no,
            ]);
        }

        // Returns
        foreach ($this->product->returns as $rd) {
            $history->push([
                'type' => 'return',
                'date' => $rd->orderReturn->created_at,
                'quantity' => $rd->quantity, // back to stock
                'unit_price' => $rd->unit_price,
                'total' => $rd->total,
                'reference' => 'Return#' . $rd->order_return_id,
            ]);
        }

        // Sort by date ascending
        $history = $history->sortBy('date')->values();

        // Add running stock
        $runningQty = 0;
        $history = $history->map(function ($item) use (&$runningQty) {
            $runningQty += $item['quantity'];
            $item['running_stock'] = $runningQty;
            return $item;
        });

        return $history;
    }
}
