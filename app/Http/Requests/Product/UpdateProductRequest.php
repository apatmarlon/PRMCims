<?php

namespace App\Http\Requests\Product;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'product_image'  => 'nullable|image|file|max:2048',
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|integer',
            'unit_id'        => 'required|integer',
            'brand_id'       => 'required|exists:brands,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity'       => 'required|integer|min:0',
            'margin_amount'       => 'nullable|numeric',
             'margin_percent'      => 'nullable|numeric',
            'quantity_alert' => 'required|integer|min:0',
            'buying_price'   => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0',
            'tax'            => 'nullable|numeric|min:0',
            'tax_type'       => 'nullable|integer',
            'notes'          => 'nullable|string|max:1000',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->name, '-'),
        ]);
    }
}
