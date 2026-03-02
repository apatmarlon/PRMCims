<?php

namespace App\Livewire\Tables;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryTable extends Component
{
    use WithPagination;

    public $perPage = 5;

    public $search = '';

    public $sortField = 'name';

    public $sortAsc = false;

    public function sortBy($field): void
    {
        if($this->sortField === $field)
        {
            $this->sortAsc = ! $this->sortAsc;

        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    public function render()
    {
        $query = Category::query()
            ->where('name', 'like', "%{$this->search}%")
            ->orderBy('name', 'asc');

        $perPage = $this->perPage == -1 ? 100000 : (int) $this->perPage;

        $categories = $query->paginate($perPage);
        return view('livewire.tables.category-table', [
            'categories' => $categories
        ]);
           
    }
}
