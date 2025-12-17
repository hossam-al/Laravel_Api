<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandWebController extends Controller
{
    public function index()
    {
        $brands = Brand::orderByDesc('id')->paginate(10, ['id','name','is_active']);
        return view('web.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('web.brands.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        Brand::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'logo' => null,
            'is_active' => (bool)($data['is_active'] ?? true),
        ]);
        return redirect()->route('web.brands.index')->with('ok', 'تم إنشاء العلامة');
    }

    public function edit(Brand $brand)
    {
        return view('web.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $brand->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => (bool)($data['is_active'] ?? true),
        ]);
        return redirect()->route('web.brands.index')->with('ok', 'تم تحديث العلامة');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return redirect()->route('web.brands.index')->with('ok', 'تم حذف العلامة');
    }
}


