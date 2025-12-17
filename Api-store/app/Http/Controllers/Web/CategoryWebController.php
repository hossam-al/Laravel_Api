<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryWebController extends Controller
{
    public function index()
    {
        $categories = Category::orderByDesc('id')->paginate(10, ['id','name','slug','is_active']);
        return view('web.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('web.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'is_active' => 'nullable|boolean',
        ]);
        Category::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'slug' => $data['slug'],
            'is_active' => (bool)($data['is_active'] ?? true),
        ]);
        return redirect()->route('web.categories.index')->with('ok', 'تم إنشاء القسم');
    }

    public function edit(Category $category)
    {
        return view('web.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'required|string|max:255|unique:categories,slug,'.$category->id,
            'is_active' => 'nullable|boolean',
        ]);
        $category->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'slug' => $data['slug'],
            'is_active' => (bool)($data['is_active'] ?? true),
        ]);
        return redirect()->route('web.categories.index')->with('ok', 'تم تحديث القسم');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('web.categories.index')->with('ok', 'تم حذف القسم');
    }
}


