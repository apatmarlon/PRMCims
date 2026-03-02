<?php

namespace App\Livewire\Tables;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderTable extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $sortField = 'invoice_no';

    public $sortAsc = false;

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortAsc = ! $this->sortAsc;

        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

 public function render()
{
    $query = Order::query()
            ->with(['customer', 'details'])
            ->when($this->search, function ($query) {
                $query->where('invoice_no', 'like', "%{$this->search}%")
                    ->orWhereHas('customer', fn($q) =>
                        $q->where('name', 'like', "%{$this->search}%")
                    );
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc');

        // handle ALL option
        $perPage = $this->perPage == -1 ? 100000 : (int) $this->perPage;

        $orders = $query->paginate($perPage);

        // Add computed fields
        $orders->getCollection()->transform(function ($order) {
            $order->total_items = $order->details->sum('quantity');
            $order->total_paid  = $order->pay ?? 0;
            $order->due         = $order->due ?? ($order->total - $order->total_paid);
            $order->payment_method = $order->payment_type ?? 'N/A';
            return $order;
        });

        return view('livewire.tables.order-table', [
            'orders' => $orders,
        ]);
    }

}
