<?php

namespace App\Http\Controllers;

use App\Models\category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display all categories (public)
     *
     * Endpoint: GET /api/v1/category
     * Auth: Yes (authenticated users)
     * Query params: search (optional) - string to search by name or id
     * Response: 200 with list of categories, 404 if none found
     */
    public function index(Request $request)
    {
        $query = category::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $categories = $query->get();

        if ($categories->isEmpty()) {
            return response([
                'status' => false,
                'message' => 'No categories found',
            ], 404);
        }

        return response([
            'status' => true,
            'message' => 'Categories retrieved successfully',
            'results' => $categories->count(),
            'data' => $categories,
        ], 200);
    }

    /**
     * Create a new category
     *
     * Endpoint: POST /api/v1/category
     * Auth: Yes (Owner only - role_id == 1)
     * Required fields (JSON or form):
     *  - name: string (required, unique)
     *  - description: string (optional)
     *  - is_active: boolean (optional)
     * Responses:
     *  - 201: category created (returns created category)
     *  - 403: forbidden (not Owner)
     *  - 422: validation error
     */
    public function store(Request $request)
    {
        // التحقق من صلاحية المستخدم
        $user = Auth::user();
        if (!$user || $user->role_id !== 1) { // only Owner can add categories
            return response([
                'status' => false,
                'message' => 'Only the Owner can create categories',
            ], 403);
        }

        // التحقق من صحة البيانات
        $request->validate([
            'name' => 'required|string|unique:categories,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // إنشاء التصنيف
        $category = category::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
        ]);

        return response([
            'status' => true,
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    /**
     * عرض تصنيف واحد بالتفصيل
     *
     * Endpoint: GET /api/v1/category/{id}
     * Auth: Yes
     * Response: 200 (category data) or 404 (not found)
     */
    public function show($id)
    {
        $category = category::find($id);

        if (!$category) {
            return response([
                'status' => false,
                'message' => 'Category not found',
            ], 404);
        }

        return response([
            'status' => true,
            'message' => 'Category retrieved successfully',
            'data' => $category,
        ], 200);
    }

    /**
     * Update an existing category
     *
     * Endpoint: POST /api/v1/category/{id}
     * Auth: Yes (Owner only - role_id == 1)
     * Request fields (any of):
     *  - name: string (sometimes, unique)
     *  - description: string (optional)
     *  - is_active: boolean (optional)
     * Responses:
     *  - 200: updated category returned
     *  - 403: forbidden (not Owner)
     *  - 404: category not found
     *  - 422: validation error
     */
    public function update(Request $request, $id)
    {
        // التحقق من صلاحية المستخدم
        $user = Auth::user();
        if (!$user || $user->role_id !== 1) { // only Owner can update categories
            return response([
                'status' => false,
                'message' => 'Only the Owner can update categories',
            ], 403);
        }

        // التحقق من وجود التصنيف
        $category = category::find($id);
        if (!$category) {
            return response([
                'status' => false,
                'message' => 'Category not found',
            ], 404);
        }

        // التحقق من صحة البيانات
        $request->validate([
            'name' => 'sometimes|string|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // تحديث التصنيف
        $category->update([
            'name' => $request->name ?? $category->name,
            'description' => $request->description ?? $category->description,
            'is_active' => $request->is_active ?? $category->is_active,
        ]);

        return response([
            'status' => true,
            'message' => 'Category updated successfully',
            'data' => $category,
        ], 200);
    }

    /**
     * Delete a category
     *
     * Endpoint: DELETE /api/v1/category/{id}
     * Auth: Yes (Owner only - role_id == 1)
     * Responses:
     *  - 200: category deleted (returns deleted category)
     *  - 403: forbidden (not Owner)
     *  - 404: category not found
     */
    public function destroy($id)
    {
        // التحقق من صلاحية المستخدم
        $user = Auth::user();
        if (!$user || $user->role_id !== 1) { // only Owner can delete categories
            return response([
                'status' => false,
                'message' => 'Only the Owner can delete categories',
            ], 403);
        }

        // التحقق من وجود التصنيف
        $category = category::find($id);
        if (!$category) {
            return response([
                'status' => false,
                'message' => 'Category not found',
            ], 404);
        }

        // حذف التصنيف
        $category->delete();

        return response([
            'status' => true,
            'message' => 'Category deleted successfully',
            'data' => $category,
        ], 200);
    }

    /**
     * Delete all categories (Owner only)
     *
     * Endpoint: DELETE /api/v1/category/DeleteAll/delete
     * Auth: Yes (Owner only - role_id == 1)
     * Notes: best-effort deletion; related foreign-key constraints may prevent deletion of some items.
     * Responses:
     *  - 200: operation attempted (returns message)
     *  - 403: forbidden (not Owner)
     */
    public function DeleteAll(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role_id !== 1) {
            return response([
                'status' => false,
                'message' => 'Only the Owner can delete all categories',
            ], 403);
        }

        $categories = category::all();
        foreach ($categories as $cat) {
            try {
                $cat->delete();
            } catch (\Exception $e) {
                // If deletion fails for related FK, continue and report later
                // We don't stop the loop to attempt best-effort deletion
            }
        }

        return response([
            'status' => true,
            'message' => 'All categories deletion attempted (Owner)',
        ], 200);
    }
}
