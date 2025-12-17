<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\products;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class ProductWebController extends Controller
{
    public function index()
    {
        $products = products::with(['Category:id,name', 'brand:id,name'])
            ->orderByDesc('id')
            ->paginate(10, ['id', 'name', 'price', 'stock', 'category_id', 'brand_id', 'is_active']);
        return view('web.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get(['id','name']);
        $brands = Brand::orderBy('name')->get(['id','name']);
        return view('web.products.create', compact('categories','brands'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sku' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'is_active' => 'nullable|boolean',
        ]);

        products::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'sku' => $data['sku'] ?? null,
            'stock' => $data['stock'],
            'category_id' => $data['category_id'] ?? null,
            'brand_id' => $data['brand_id'] ?? null,
            'is_active' => (bool)($data['is_active'] ?? true),
        ]);

        return redirect()->route('web.products.index')->with('ok', 'تم إنشاء المنتج');
    }

    public function edit(products $product)
    {
        $categories = Category::orderBy('name')->get(['id','name']);
        $brands = Brand::orderBy('name')->get(['id','name']);
        return view('web.products.edit', compact('product','categories','brands'));
    }

    public function update(Request $request, products $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sku' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'is_active' => 'nullable|boolean',
        ]);

        $product->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'sku' => $data['sku'] ?? null,
            'stock' => $data['stock'],
            'category_id' => $data['category_id'] ?? null,
            'brand_id' => $data['brand_id'] ?? null,
            'is_active' => (bool)($data['is_active'] ?? true),
        ]);

        return redirect()->route('web.products.index')->with('ok', 'تم تحديث المنتج');
    }

    public function destroy(products $product)
    {
        $product->delete();
        return redirect()->route('web.products.index')->with('ok', 'تم حذف المنتج');
    }
}


