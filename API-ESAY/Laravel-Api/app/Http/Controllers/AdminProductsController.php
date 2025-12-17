<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\AdminProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminProductsController extends Controller
{
    /**
     * عرض جميع المنتجات الخاصة بالأدمن المسجل دخوله
     */
    public function index(Request $request)
    {
        $user = $request->user();


        // تأكد من أن المستخدم هو أدمن
        if ($user->role_id !== 2) {
            return response([
                'status' => false,
                'message' => 'Only Admin users can access admin products',
            ], 403);
        }

        // ابني الاستعلام أولاً ثم نفّذ get() بعد تطبيق أي فلاتر مثل البحث
        $query = AdminProduct::where('user_id', $user->id)
            ->with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->get();

        if ($products->isEmpty()) {
            return response([
                'status' => false,
                'message' => 'No admin products found',
            ], 404);
        }

        return response([
            'status' => true,
            'message' => 'Admin products retrieved successfully',
            'results' => $products->count(),
            'data' => $products->makeHidden(['user_id']),
        ], 200);
    }

    /**
     * إضافة منتج جديد للأدمن
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // تأكد من أن المستخدم هو أدمن
        if ($user->role_id !== 2) {
            return response([
                'status' => false,
                'message' => 'Only Admin users can add admin products',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'sku' => 'nullable|string|unique:admin_products,sku',
            'stock' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('image_url')) {
            $image_data = $request->file('image_url');
            $image_name = time() . '_' . $image_data->getClientOriginalName();
            $location   = public_path('upload');
            $image_data->move($location, $image_name);
            $image_path = url('upload/' . $image_name);
        } else {
            $image_path = null;
        }
        $sku = strtoupper(Str::random(10));
        // إنشاء المنتج الجديد مع إضافة معرف المستخدم الحالي
        $product = AdminProduct::create([
            'name' => $request->name,
            'description' => $request->description ?? null,
            'price' => $request->price,
            'image_url' => $image_path,
            'sku' => $request->sku ??   $sku,
            'stock' => $request->stock ?? 0,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
            'is_featured' => $request->has('is_featured') ? $request->is_featured : false,
            'category_id' => $request->category_id,
            'user_id' => $user->id,
        ]);

        return response([
            'status' => true,
            'message' => 'Admin product added successfully',
            'data' => $product->makeHidden(['user_id']),
        ], 201);
    }

    /**
     * عرض منتج محدد للأدمن
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        // تأكد من أن المستخدم هو أدمن
        if ($user->role_id !== 2) {
            return response([
                'status' => false,
                'message' => 'Only Admin users can view admin products',
            ], 403);
        }

        $product = AdminProduct::where('id', $id)
            ->where('user_id', $user->id)
            ->with('category')
            ->first();

        if (!$product) {
            return response([
                'status' => false,
                'message' => 'Product not found or you do not have permission to view it',
            ], 404);
        }

        return response([
            'status' => true,
            'data' => $product->makeHidden(['user_id'])
        ], 200);
    }

    /**
     * تحديث منتج محدد للأدمن
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        // تأكد من أن المستخدم هو أدمن
        if ($user->role_id !== 2) {
            return response([
                'status' => false,
                'message' => 'Only Admin users can update admin products',
            ], 403);
        }

        $product = AdminProduct::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$product) {
            return response([
                'status' => false,
                'message' => 'Product not found or you do not have permission to update it',
            ], 404);
        }

        $mega = 2 * 1024; // 2MB لكل صورة

        // التحقق من صحة البيانات
        $request->validate([
            'name'        => "sometimes|string|max:255",
            'description' => "sometimes|string|min:3|max:255",
            'price'       => "sometimes|numeric|min:0",
            'sku'         => "nullable|string|unique:products,sku," . $id,
            'stock'       => "sometimes|integer|min:0",
            'category_id' => "nullable|exists:categories,id",
            'is_active'   => "boolean",
            'is_featured' => "boolean",
            'image_url'   => "nullable|file|mimes:jpg,jpeg,png,webp|max:$mega"
        ]);

        // معالجة الصورة
        if ($request->hasFile('image_url')) {
            // حذف الصورة القديمة
            if ($product->image_url) {
                $oldImagePath = public_path('upload/' . basename($product->image_url));
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // رفع الصورة الجديدة
            $image_data = $request->file('image_url');
            $image_name = time() . '_' . $image_data->getClientOriginalName();
            $location   = public_path('upload');
            $image_data->move($location, $image_name);
            $image_path = url('upload/' . $image_name);
        } else {
            $image_path = $product->image_url;
        }

        // تحديث المنتج - لا تغير user_id عند التعديل
        $product->update([
            'name'        => $request->name ?? $product->name,
            'description' => $request->description ?? $product->description,
            'price'       => $request->price ?? $product->price,
            'stock'       => $request->stock ?? $product->stock,
            'category_id' => $request->category_id ?? $product->category_id,
            'is_active'   => $request->is_active ?? $product->is_active,
            'is_featured' => $request->is_featured ?? $product->is_featured,
            'image_url'   => $image_path,
        ]);

        // إضافة علامة على المنتجات التي يملكها المستخدم
        $product->my_product = ($product->user_id === $user->id);

        // تحميل بيانات الفئة وإخفاء بعض الحقول
        $product->load('category');

        return response([
            'status'  => true,
            'message' => 'Product updated successfully',
            'data'    => $product,
            'user_role' => $user->role->slug ?? null,
        ], 200);
    }

    /**
     * حذف منتج محدد للأدمن
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        // تأكد من أن المستخدم هو أدمن
        if ($user->role_id !== 2) {
            return response([
                'status' => false,
                'message' => 'Only Admin users can delete admin products',
            ], 403);
        }

        $product = AdminProduct::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$product) {
            return response([
                'status' => false,
                'message' => 'Product not found or you do not have permission to delete it',
            ], 404);
        }
        // حذف الصورة إذا كانت موجودة
        if ($product->image_url) {
            $imagePath = public_path('upload/' . basename($product->image_url));
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $product->delete();

        return response([
            'status' => true,
            'message' => 'Admin product deleted successfully'
        ], 200);
    }

    /**
     * حذف جميع منتجات الأدمن
     */
    public function DeleteAll(Request $request)
    {
        $user = $request->user();

        // تأكد من أن المستخدم هو أدمن
        if ($user->role_id !== 2) {
            return response([
                'status' => false,
                'message' => 'Only Admin users can delete admin products',
            ], 403);
        }

        // احصل على جميع منتجات الأدمن
        $products = AdminProduct::where('user_id', $user->id)->get();

        // حذف الصور
        foreach ($products as $product) {
            if ($product->image_url) {
                $imagePath = public_path('upload/' . basename($product->image_url));
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $product->delete();
        }

        return response([
            'status' => true,
            'message' => 'All your admin products deleted successfully',
        ], 200);
    }
}
