<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::orderBy('name')->paginate(20);

        return view('brands.index', compact('brands'));
    }

    public function create()
    {
        return view('brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'note' => 'nullable|string',
        ]);

        Brand::create($request->only('name', 'note'));

        return redirect()->route('brands.index')->with('success', 'Brand has been created!');
    }

    public function show(Brand $brand)
    {
        return view('brands.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        return view('brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'note' => 'nullable|string',
        ]);

        $brand->update($request->only('name', 'note'));

        return redirect()->route('brands.index')->with('success', 'Brand has been updated!');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()->route('brands.index')->with('success', 'Brand has been deleted!');
    }
}
