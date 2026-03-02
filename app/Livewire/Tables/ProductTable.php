<?php

namespace App\Livewire\Tables;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductTable extends Component
{
    use WithPagination;

    public $perPage = 25;

    public $search = '';

    public $sortField = 'id';

    public $sortAsc = false;
    public function updatingSearch()
    {
        $this->resetPage();
    }
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
        $query = Product::query()
            ->with(['category', 'unit', 'brand', 'supplier'])
            ->search($this->search)
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc');

        // Handle All option
        $perPage = $this->perPage == -1 ? 100000 : (int) $this->perPage;

        $products = $query->paginate($perPage);

        return view('livewire.tables.product-table', [
            'products' => $products,
        ]);
    }
}
