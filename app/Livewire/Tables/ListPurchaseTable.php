<?php

namespace App\Livewire\Tables;

use App\Models\Purchase;
use Livewire\Component;
use Livewire\WithPagination;

class ListPurchaseTable extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $sortField = 'purchase_no';

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
       $query = Purchase::query()
            ->with('supplier')
            ->where('status', 1)
            ->search($this->search)
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc');

        // if -1 (All) use large number
        $perPage = $this->perPage == -1 ? 100000 : (int) $this->perPage;

        return view('livewire.tables.list-purchase-table', [
            'purchases' => $query->paginate($perPage),
        ]);
    }
}
