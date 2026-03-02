<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Models\Customer;
use Gloudemans\Shoppingcart\Facades\Cart;

class InvoiceController extends Controller
{
    public function create(StoreInvoiceRequest $request, Customer $customer)
    {
        $customer = Customer::query()
            ->where('id', $request->get('customer_id'))
            ->first();

        $note = $request->get('note');

        return view('invoices.index', [
            'customer' => $customer,
            'carts' => Cart::instance('order')->content(),
            'note' => $note,
            'order_date' => $request->order_date,
        ]);
    }
}
