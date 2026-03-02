<?php

namespace App\Livewire;

use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Order;
use App\Models\OrderDetails;

class OrderForm extends Component
{
    public string $note = ''; // <-- add this

    public $cart_instance;

    private $product;
    public string $order_date;
    #[Validate('Required')]
    public int $taxes = 0;

    public array $invoiceProducts = [];

    public array $productSearch = [];
    public array $productSuggestions = [];

    #[Validate('required', message: 'Please select products')]
    public Collection $allProducts;

    public function mount($cartInstance): void
    {
        $this->cart_instance = $cartInstance;

        $this->allProducts = Product::all();

        //$cart_items = Cart::instance($this->cart_instance)->content();
    }

    public function render(): View
    {
        $total = 0;

        foreach ($this->invoiceProducts as $invoiceProduct) {
            if ($invoiceProduct['is_saved'] && $invoiceProduct['product_price'] && $invoiceProduct['quantity']) {
               $total += ($invoiceProduct['product_price'] + $invoiceProduct['markup_price']) * $invoiceProduct['quantity'];
            }
        }

        $cart_items = Cart::instance($this->cart_instance)->content();

        return view('livewire.order-form', [
            'subtotal' => $total,
            'total' => $total * (1 + (is_numeric($this->taxes) ? $this->taxes : 0) / 100),
            'cart_items' => $cart_items,
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
            'unit_name' => '',
            'product_name' => '',
            'markup_price'  => 0,
            'product_price' => 0,
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

        if (!$product) {
            $this->addError("invoiceProducts.$index", "Please select a valid product.");
            return;
        }

        $quantity = $this->invoiceProducts[$index]['quantity'];

        if ($quantity > $product->quantity) {
            $this->addError("invoiceProducts.$index", "Quantity cannot exceed available stock ({$product->quantity}).");
            return;
        }

        $markup = (float) $this->invoiceProducts[$index]['markup_price'];
        $unitCost = (float) $product->selling_price;

        $this->invoiceProducts[$index]['product_name'] = $product->name;
        $this->invoiceProducts[$index]['product_price'] = $unitCost;
        $this->invoiceProducts[$index]['is_saved'] = true;

        $cart = Cart::instance($this->cart_instance);

        $exists = $cart->search(function ($cartItem) use ($product) {
            return $cartItem->id === $product['id'];
        });

        if ($exists->isNotEmpty()) {
            session()->flash('message', 'Product exists in the cart!');
            return;
        }

        $cart->add([
            'id' => $product->id,
            'name' => $product->name,
            'price' =>  $unitCost,
            'qty' => $quantity,
            'weight' => 1,
            'options' => [
                'code' => $product->code,
                'unitcost' => $product->selling_price,
                'markup' => $markup,
            ],
        ]);
    }

    public function removeProduct($index): void
    {
        unset($this->invoiceProducts[$index]);

        $this->invoiceProducts = array_values($this->invoiceProducts);

        //
        //Cart::instance($this->cart_instance)->remove($index);
    }

    public function saveOrder(): void
{
    $this->validate([
        'selectedCustomer' => 'required|exists:customers,id',
        'invoiceProducts' => 'required|array|min:1',
        'note' => 'nullable|string|max:255',
        'paymentType' => 'required|string',
        'order_date'       => 'required|date',
    ]);

    // Create order
    $order = Order::create([
        'customer_id' => $this->selectedCustomer,
        'order_date' => $this->order_date,
        'payment_type' => $this->paymentType,
        'note' => $this->note, // <-- this is where note is saved
        'sub_total' => $this->calculateSubtotal(),
        'vat' => $this->taxes,
        'total' => $this->calculateTotal(),
        'added_by' => auth()->user()->name,
    ]);

    // Save order details
    foreach ($this->invoiceProducts as $product) {
        OrderDetails::create([
            'order_id' => $order->id,
            'product_id' => $product['product_id'],
            'quantity' => $product['quantity'],
            'unitcost'    => $product['product_price'],
            'markup_price'=> $product['markup_price'],
            'total' => ($product['product_price'] + $product['markup_price']) * $product['quantity'],
        ]);
    }
    

    // Clear cart and reset form
    Cart::instance($this->cart_instance)->destroy();
    $this->reset(['invoiceProducts', 'note', 'taxes', 'selectedCustomer', 'paymentType']);
    session()->flash('message', 'Order created successfully!');
    }
    public function calculateSubtotal(): float
    {
        $subtotal = 0;

        foreach ($this->invoiceProducts as $product) {
            if (
                isset($product['product_price'], $product['markup_price'], $product['quantity'])
            ) {
                $subtotal +=
                    ($product['product_price'] + $product['markup_price'])
                    * $product['quantity'];
            }
        }

        return $subtotal;
    }

    public function calculateTotal(): float
    {
        $taxRate = is_numeric($this->taxes) ? $this->taxes : 0;

        return $this->calculateSubtotal()
            * (1 + ($taxRate / 100));
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
        $this->invoiceProducts[$index]['product_unit_name'] = $product->unit?->name ?? 'No Unit';
        $this->invoiceProducts[$index]['product_price'] = $product->selling_price;

        $this->productSearch[$index] =  $product->name . ' - ' . ($product->brand?->name ?? 'No Brand') . ' - ' . ($product->unit?->name ?? 'No Unit');
        $this->productSuggestions[$index] = [];
    }
}
