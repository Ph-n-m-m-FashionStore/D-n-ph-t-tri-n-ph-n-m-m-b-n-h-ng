<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    public function create()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'brand' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:20',
            'gender' => 'required|in:nam,nu,tatca',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'is_active' => 'nullable|boolean'
        ]);

        // Upload ảnh
        $imagePath = $request->file('image')->store('products', 'public');

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'brand' => $request->brand,
            'size' => $request->size,
            'gender' => $request->gender,
            'image_url' => $imagePath,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return redirect()->route('products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function edit(Product $product)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        $data = $request->only(['name', 'description', 'price', 'stock', 'brand', 'size']);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if ($product->image_url) {
                Storage::disk('public')->delete($product->image_url);
            }
            $data['image_url'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy(Product $product)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        if ($product->image_url) {
            Storage::disk('public')->delete($product->image_url);
        }
        
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Xóa sản phẩm thành công!');
    }
}