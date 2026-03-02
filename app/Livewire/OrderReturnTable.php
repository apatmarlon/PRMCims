<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\OrderReturn;

class OrderReturnTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    // Reset page when searching
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $returns = OrderReturn::with(['order', 'customer'])
            ->where(function ($q) {
                $q->where('id', 'like', "%{$this->search}%")
                  ->orWhereHas('order', function ($q) {
                      $q->where('invoice_no', 'like', "%{$this->search}%");
                  })
                  ->orWhereHas('customer', function ($q) {
                      $q->where('name', 'like', "%{$this->search}%");
                  })
                  ->orWhere('processed_by', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.order-return-table', compact('returns'));
    }
}
