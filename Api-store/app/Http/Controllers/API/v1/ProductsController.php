<?php

namespace App\Http\Controllers\API\v1;

use App\Models\products;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    /**
     * عرض كل المنتجات
     */
    public function index()
    {
        $products = products::with('images', 'Category', 'brand')->get();
        // ->paginate(10)
        if ($products->isEmpty()) {
            return response([
                'message' => 'No data found',
                'status' => 404
            ], 404);
        }

        return response([
            'data' => $products,
            'message' => 'Get all data successfully',
            'status' => 200,
        ], 200);
    }

    /**
     * إضافة منتج جديد
     */
    public function store(Request $request)
    {
        $mega = 2 * 1024; // 2MB لكل صورة
        $request->validate([
            'name'        => "required|string|max:255",
            'description' => "required|string|min:3|max:255",
            'price'       => "required|numeric|min:0",
            'sku'         => "nullable|string|unique:products,sku",
            'stock'       => "required|integer|min:0",
            'category_id' => "nullable|exists:categories,id",
            'brand_id'    => "nullable|exists:brands,id",
            'is_active'   => "boolean",
            'image'    => "nullable|file|mimes:jpg,jpeg,png,webp|max:$mega"
        ]);

        // إنشاء المنتج
        $product = products::create([
            'user_id'     => Auth::id(),
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'sku'         => $request->sku,
            'stock'       => $request->stock,
            'category_id' => $request->category_id,
            'brand_id'    => $request->brand_id,
            'is_active'   => $request->is_active ?? true,
        ]);


        if ($request->hasFile('image')) {
            $images = is_array($request->file('image')) ? $request->file('image') : [$request->file('image')];

            foreach ($images as $key => $image) {
                $image_name = time() . '_' . $image->getClientOriginalName();
                $location = public_path('upload');
                $image->move($location, $image_name);
                $image_path = url('upload/' . $image_name);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $image_path,
                    'is_primary' => $key == 0, // أول صورة أساسية
                ]);
            }
        }

        return response([
            'data' => $product->load('images', 'brand', 'Category'),

            'message' => 'Product added successfully',
            'status' => 201
        ], 201);
    }

    /**
     * عرض منتج واحد
     */
    public function show($id)
    {
        $product = products::with('images')->find($id);

        if (!$product) {
            return response([
                'message' => 'Product not found',
                'status' => 404
            ], 404);
        }

        return response([
            'data' => $product,
            'message' => 'Get product successfully',
            'status' => 200
        ], 200);
    }

    /**
     * تحديث المنتج
     */
    public function update(Request $request, $id)
    {
        $product = products::find($id);

        if (!$product) {
            return response([
                'message' => 'Product not found',
                'status' => 404
            ], 404);
        }

        $mega = 2 * 1024;
        $request->validate([
            'name'        => "sometimes|string|max:255",
            'description' => "sometimes|string|min:3|max:255",
            'price'       => "sometimes|numeric|min:0",
            'sku'         => "nullable|string|unique:products,sku," . $id,
            'stock'       => "sometimes|integer|min:0",
            'category_id' => "nullable|exists:categories,id",
            'brand_id'    => "nullable|exists:brands,id",
            'is_active'   => "boolean",
            'images.*'    => "nullable|file|mimes:jpg,jpeg,png|max:$mega"
        ]);

        $product->update([
            'user_id'     => Auth::id(),
            'name'        => $request->name ?? $product->name,
            'description' => $request->description ?? $product->description,
            'price'       => $request->price ?? $product->price,
            'sku'         => $request->sku ?? $product->sku,
            'stock'       => $request->stock ?? $product->stock,
            'category_id' => $request->category_id ?? $product->category_id,
            'brand_id'    => $request->brand_id ?? $product->brand_id,
            'is_active'   => $request->is_active ?? $product->is_active,
        ]);

        // لو عايز يرفع صور جديدة
        if ($request->hasFile('images')) {
            // امسح الصور القديمة من السيرفر والجدول
            foreach ($product->images as $img) {
                $imagePath = public_path('upload/' . basename($img->image));
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $img->delete();
            }

            // ضيف الجديد
            foreach ($request->file('images') as $key => $image) {
                $image_name = time() . '_' . $image->getClientOriginalName();
                $location = public_path('upload');
                $image->move($location, $image_name);
                $image_path = url('upload/' . $image_name);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $image_path,
                    'is_primary' => $key == 0,
                ]);
            }
        }

        return response([
            'data' => $product->load('images', 'brand', 'Category'),
            'message' => 'Product updated successfully',
            'status' => 200
        ], 200);
    }

    /**
     * حذف منتج
     */
    public function destroy($id)
    {
        $product = products::with('images')->find($id);

        if (!$product) {
            return response([
                'message' => 'Product not found',
                'status' => 404
            ], 404);
        }

        // امسح الصور من السيرفر
        foreach ($product->images as $img) {
            $imagePath = public_path('upload/' . basename($img->image));
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $img->delete();
        }

        $product->delete();

        return response([
            'message' => 'Product deleted successfully',
            'status' => 200
        ], 200);
    }
    public function showBySlug($slug)
    {
        $products = products::where('slug', $slug)->first();
        if (!$products) {
            return response([
                'message' => 'Not found',
                'status'  => 404,
            ], 404);
        }
        return response()->json([
            'status' => true,
            'data' => $products
        ]);
    }

    /**
     * مسح كل المنتجات
     */
    public function DeleteAll()
    {
        $allProducts = products::with('images')->get();

        foreach ($allProducts as $product) {
            foreach ($product->images as $img) {
                $imagePath = public_path('upload/' . basename($img->image));
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $img->delete();
            }
            $product->delete();
        }

        return response([
            'message' => 'All products deleted successfully',
            'status' => 200
        ], 200);
    }
}
