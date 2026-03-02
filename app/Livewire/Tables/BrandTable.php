<?php

namespace App\Livewire\Tables;

use App\Models\Brand;
use Livewire\Component;
use Livewire\WithPagination;

class BrandTable extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $sortField = 'name';

    public $sortAsc = true;

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
       $query = Brand::query()
            ->where('name', 'like', "%{$this->search}%")
            ->orderBy('name', 'asc');

        $perPage = $this->perPage == -1 ? 100000 : (int) $this->perPage;

        $brands = $query->paginate($perPage);
        return view('livewire.tables.brand-table', [
           'brands' => $brands
    ]);
    }
}
