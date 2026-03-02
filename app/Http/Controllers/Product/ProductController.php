<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorHTML;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Supplier;
use App\Services\ProductHistoryService;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::select('id', 'name')
            ->limit(1)
            ->get();

        return view('products.index', [
            'products' => $products,
        ]);
    }

    public function create(Request $request)
    {
        $categories = Category::all(['id', 'name']);
        $units = Unit::all(['id', 'name']);
        $brands = Brand::all(['id', 'name']); // Fetch all brands

        if ($request->has('category')) {
            $categories = Category::whereSlug($request->get('category'))->get();
        }

        if ($request->has('unit')) {
            $units = Unit::whereSlug($request->get('unit'))->get();
        }

        return view('products.create', [
            'categories' => $categories,
            'units' => $units,
            'brands' => $brands, // Pass brands to the view
            'suppliers'  => Supplier::select('id','name')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
      

        try {
            $product = Product::create($request->all());

            /**
             * Handle image upload
             */
            if ($request->hasFile('product_image')) {
                $file = $request->file('product_image');
                $filename = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

                // Validate file before uploading
                if ($file->isValid()) {
                    $file->storeAs('products/', $filename, 'public');
                    $product->update([
                        'product_image' => $filename
                    ]);
                } else {
                    return back()->withErrors(['product_image' => 'Invalid image file']);
                }
            }

            return redirect()
                ->back()
                ->with('success', 'Product has been created with code: ' . $product->code);

        } catch (\Exception $e) {
            // Handle any unexpected errors
            return back()->withErrors(['error' => 'Something went wrong while creating the product']);
        }
    }

    // Helper method to generate a unique product code
  

    public function show(Product $product)
    {
        // Generate a barcode
        $generator = new BarcodeGeneratorHTML();

        $barcode = $generator->getBarcode($product->code, $generator::TYPE_CODE_128);

        return view('products.show', [
            'product' => $product,
            'barcode' => $barcode,
        ]);
    }

    public function edit(Product $product)
    {
        return view('products.edit', [
            'categories' => Category::all(),
            'units' => Unit::all(),
            'brands' => Brand::all(['id', 'name']), // <-- add this line
            'suppliers'  => Supplier::select('id','name')->orderBy('name')->get(),
            'product' => $product
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->except('product_image'));

        if ($request->hasFile('product_image')) {

            // Delete old image if exists
            if ($product->product_image) {
                \Storage::disk('public')->delete('products/' . $product->product_image);
            }

            // Prepare new image
            $file = $request->file('product_image');
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

            // Store new image to public storage
            $file->storeAs('products/', $fileName, 'public');

            // Save new image name to database
            $product->update([
                'product_image' => $fileName
            ]);
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Product has been updated!');
    }

    public function destroy(Product $product)
    {
        /**
         * Delete photo if exists.
         */
        if ($product->product_image) {
            \Storage::disk('public')->delete('products/' . $product->product_image);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product has been deleted!');
    }
   public function history(Product $product)
    {
        $historyService = new ProductHistoryService($product);
        $history = $historyService->getHistory();

        return view('products.history', compact('product', 'history'));
    }
}
