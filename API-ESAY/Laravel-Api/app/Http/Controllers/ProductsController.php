<?php

namespace App\Http\Controllers;

use App\Models\products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $query = products::with('category');
        $user = Auth::user();

        // Apply search filters if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('price', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%");
                    });
            });
        }


        $products = $query->get();
        if ($products->isEmpty()) {
            return response([
                'status' => false,
                'message' => 'No products found',
            ], 404);
        } else {
            // إظهار المنتجات
            return response([
                'status' => true,
                'message' => 'Products retrieved successfully',
                'results' => $products->count(),
                'data' => $products->makeHidden(['user_id', 'category_id']),
            ], 200);

            // للسوبر أدمن، إظهار من أنشأ كل منتج
            if ($user && $user->role_id == 1) {
                $products->load('user:id,name,email');

                // إضافة معلومات عن مالك كل منتج
                foreach ($products as $product) {
                    $product->owner_info = $product->user ? [
                        'id' => $product->user->id,
                        'name' => $product->user->name,
                    ] : null;
                }
            } else {
                // إخفاء معلومات المستخدم للزوار العاديين
                $products->makeHidden(['user_id']);
            }

            return response([
                'status' => true,
                'message' => 'Products retrieved successfully',
                'results' => $products->count(),
                'data' => $products
            ], 200);
        }
    }

    /**
     * إضافة منتج جديد (للسوبر أدمن فقط)
     */
    public function store(Request $request)
    {
        // التحقق من صلاحية المستخدم
        $user = Auth::user();
        if (!$user || $user->role_id !== 1) { // فقط السوبر أدمن يمكنه الإضافة
            return response([
                'status' => false,
                'message' => 'Only Super Admin can add products to main catalog',
            ], 403);
        }

        $mega = 2 * 1024; // 2MB لكل صورة

        // التحقق من صحة البيانات
        $request->validate([
            'name'        => "required|string|max:255",
            'description' => "required|string|min:3|max:255",
            'price'       => "required|numeric|min:0",
            'sku'         => "nullable|string|unique:products,sku",
            'stock'       => "required|integer|min:0",
            'category_id' => "nullable|exists:categories,id",
            'is_active'   => "boolean",
            'is_featured' => "boolean",
            'image_url'   => "nullable|file|mimes:jpg,jpeg,png,webp|max:$mega"
        ]);

        // معالجة الصورة
        if ($request->hasFile('image_url')) {
            $image_data = $request->file('image_url');
            $image_name = time() . '_' . $image_data->getClientOriginalName();
            $location   = public_path('upload');
            $image_data->move($location, $image_name);
            $image_path = url('upload/' . $image_name);
        } else {
            $image_path = null;
        }

        // إنشاء المنتج
        $product = products::create([
            'user_id'     => Auth::id(),
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'category_id' => $request->category_id,
            'is_active'   => $request->is_active ?? true,
            'is_featured' => $request->is_featured ?? true,
            'image_url'   => $image_path,
        ]);

        return response([
            'status'  => true,
            'message' => 'Product added successfully',
            'data'    => $product->load('category')->makeHidden(['user_id', 'category_id']),
        ], 201);
    }

    /**
     * عرض منتج واحد بالتفصيل
     */
    public function show($id)
    {
        $product = products::with('category')->find($id);

        if (!$product) {
            return response([
                'status' => false,
                'message' => 'Product not found',
            ], 404);
        }

        return response([
            'status' => true,
            'message' => 'Product retrieved successfully',
            'data' => $product->makeHidden(['user_id', 'category_id']),
        ], 200);
    }


    /**
     * تحديث المنتج (للأدمن فقط: للمنتجات الخاصة به، وللسوبر أدمن: لجميع المنتجات)
     */
    public function update(Request $request, $id)
    {
        // التحقق من صلاحية المستخدم
        $user = Auth::user();
        if (!$user) {
            return response([
                'status' => false,
                'message' => 'Authentication required',
            ], 401);
        }

        $product = products::find($id);
        if (!$product) {
            return response([
                'status' => false,
                'message' => 'Product not found',
            ], 404);
        }

        // تحقق من صلاحية التعديل: فقط السوبر أدمن يمكنه تعديل منتجات الكتالوج الرئيسي
        if ($user->role_id !== 1) {
            return response([
                'status' => false,
                'message' => 'Only Super Admin can update products in the main catalog',
            ], 403);
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
     * حذف منتج (للأدمن فقط: للمنتجات الخاصة به، وللسوبر أدمن: لجميع المنتجات)
     */
    public function destroy($id)
    {
        // التحقق من صلاحية المستخدم
        $user = Auth::user();
        if (!$user) {
            return response([
                'status' => false,
                'message' => 'Authentication required',
            ], 401);
        }

        $product = products::find($id);
        if (!$product) {
            return response([
                'status' => false,
                'message' => 'Product not found',
            ], 404);
        }

        // تحقق من صلاحية الحذف: فقط السوبر أدمن يمكنه حذف منتجات الكتالوج الرئيسي
        if ($user->role_id !== 1) {
            return response([
                'status' => false,
                'message' => 'Only Super Admin can delete products from the main catalog',
            ], 403);
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
            'status'  => true,
            'message' => 'Product deleted successfully',
        ], 200);
    }

    /**
     * حذف كل المنتجات (للسوبر أدمن فقط)
     */
    public function DeleteAll()
    {
        // التحقق من صلاحية المستخدم
        $user = Auth::user();
        if (!$user || $user->role_id !== 1) {
            return response([
                'status' => false,
                'message' => 'Only Super Admin can delete products from the main catalog',
            ], 403);
        }

        // حذف جميع المنتجات
        $products = products::all();

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
            'status'  => true,
            'message' => 'All products deleted successfully',
        ], 200);
    }
}
