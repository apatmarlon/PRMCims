<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PurchaseForm extends Component
{
    #[Validate('Required')]
    public int $taxes = 0;
    public float $discount_percentage = 0;
    public float $discount_amount = 0;
    public array $invoiceProducts = [];
    public array $productSearch = [];
    public array $productSuggestions = [];

    #[Validate('required', message: 'Please select products')]
    public Collection $allProducts;

    // Add search terms for live filtering
    public array $searchTerms = [];

    public function mount(): void
    {
        $this->allProducts = Product::all();
    }

    public function render(): View
    {
        $subtotal = 0;

       foreach ($this->invoiceProducts as $invoiceProduct) {
            if (!$invoiceProduct['is_saved']) continue;

            if (!empty($invoiceProduct['is_freebie'])) {
                continue; // 👈 SKIP FREEBIES
            }

            $price = $invoiceProduct['product_price'];
            $qty = $invoiceProduct['quantity'];
            $percent = $invoiceProduct['discount_percentage'] ?? 0;

            $discount_amount = (($price * $qty) * $percent) / 100;
            $row_total = ($price * $qty) - $discount_amount;

            $subtotal += $row_total;
        }

        $taxRate = is_numeric($this->taxes) ? $this->taxes : 0;
        $taxAmount = $subtotal * ($taxRate / 100);
        $total = $subtotal + $taxAmount;

        return view('livewire.purchase-form', [
            'subtotal' => $subtotal,
            'total' => $total,
        ]);
    }

    public function addProduct(): void
    {
        foreach ($this->invoiceProducts as $key => $invoiceProduct) {
            if (! $invoiceProduct['is_saved']) {
                $this->addError('invoiceProducts.'.$key, 'This line must be saved before creating a new one.');
                return;
            }
        }

        $this->invoiceProducts[] = [
            'product_id' => '',
            'quantity' => 1,
            'is_saved' => false,
            'product_name' => '',
            'product_price' => 0,
            'discount_percentage' => 0,
            'is_freebie' => false,
        ];
        $index = array_key_last($this->invoiceProducts);

        $this->productSearch[$index] = '';
        $this->productSuggestions[$index] = [];
    }

    public function editProduct($index): void
    {
        foreach ($this->invoiceProducts as $key => $invoiceProduct) {
            if (! $invoiceProduct['is_saved']) {
                $this->addError('invoiceProducts.'.$key, 'This line must be saved before editing another.');
                return;
            }
        }

        $this->invoiceProducts[$index]['is_saved'] = false;
    }

    public function saveProduct($index): void
    {
        $this->resetErrorBag();

        $product = $this->allProducts->find($this->invoiceProducts[$index]['product_id']);

        $this->invoiceProducts[$index]['product_name'] = $product->name;

        if ($this->invoiceProducts[$index]['is_freebie']) {
            $this->invoiceProducts[$index]['product_price'] = 0;
            $this->invoiceProducts[$index]['discount_percentage'] = 0;
        } else {
            $this->invoiceProducts[$index]['product_price'] = $product->buying_price;
        }

        $this->invoiceProducts[$index]['is_saved'] = true;


        // Reset search term after selection
        $this->searchTerms[$index] = '';
    }

    public function removeProduct($index): void
    {
        unset($this->invoiceProducts[$index]);
        $this->invoiceProducts = array_values($this->invoiceProducts);
    }
       public function updatedProductSearch($value, $key)
    {
        if (strlen($value) < 2) {
            $this->productSuggestions[$key] = [];
            return;
        }

        $this->productSuggestions[$key] = Product::where('name', 'like', "%{$value}%")
            ->get();
    }
    public function selectProduct($index, $productId)
    {
        $product = Product::findOrFail($productId);

        $this->invoiceProducts[$index]['product_id'] = $product->id;
        $this->invoiceProducts[$index]['product_name'] = $product->name;
        $this->invoiceProducts[$index]['product_brand_name'] = $product->brand?->name ?? 'No Brand';
        $this->invoiceProducts[$index]['product_price'] = $product->buying_price;

        $this->productSearch[$index] =  $product->name . ' - ' . ($product->brand?->name ?? 'No Brand');
        $this->productSuggestions[$index] = [];
    }
    public function updatedInvoiceProducts($value, $key)
    {
        if (str_ends_with($key, '.is_freebie')) {
            $index = explode('.', $key)[0];

            if ($this->invoiceProducts[$index]['is_freebie']) {
                $this->invoiceProducts[$index]['product_price'] = 0;
                $this->invoiceProducts[$index]['discount_percentage'] = 0;
            } else {
                if (!empty($this->invoiceProducts[$index]['product_id'])) {
                    $product = $this->allProducts->find($this->invoiceProducts[$index]['product_id']);
                    $this->invoiceProducts[$index]['product_price'] = $product?->buying_price ?? 0;
                }
            }
        }
    }

    
}
