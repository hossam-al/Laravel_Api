<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::latest()->get();
        if ($brands->isEmpty()) {
            return response([
                'message' => 'No data found',
                'status'  => 404
            ], 404);
        }

        return response([
            'data'    => $brands,
            'message' => 'Get all data successfully',
            'status'  => 200,
        ], 200);
    }

    public function store(Request $request)
    {
        $mega = 1 * 1024;
        $request->validate([
            'name'        => 'required|string|unique:brands,name',
            'logo'        => "required|file|max:$mega",
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            $image_data = $request->file('logo');
            $image_name = time() . '_' . $image_data->getClientOriginalName();
            $location   = public_path('upload');
            $image_data->move($location, $image_name);
            $image_path = url('upload/' . $image_name);
        } else {
            $image_path = null;
        }

        $brand = Brand::create([
            'name'        => $request->name,
            'logo'        => $image_path,
            'description' => $request->description,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Brand created successfully',
            'data'    => $brand
        ], 201);
    }

    public function show($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response([
                'message' => 'Not found',
                'status'  => 404
            ], 404);
        }

        return response([
            'data'    => $brand,
            'message' => 'Get data successfully',
            'status'  => 200,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response([
                'message' => 'Product not found',
                'status' => 404
            ], 404);
        }
        $mega = 1 * 1024;
        $request->validate([
            'name'        => 'required|string|unique:brands,name,' . $id,
            'logo'        => "nullable|file|max:$mega",
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            // حذف القديم
            $imagePath = public_path('upload/' . basename($brand->logo));
            if ($brand->logo && file_exists($imagePath)) {
                unlink($imagePath);
            }

            $image_data = $request->file('logo');
            $image_name = time() . '_' . $image_data->getClientOriginalName();
            $location   = public_path('upload');
            $image_data->move($location, $image_name);
            $image_path = url('upload/' . $image_name);
        } else {
            $image_path = $brand->logo;
        }

        $brand->update([
            'name'        => $request->name,
            'logo'        => $image_path,
            'description' => $request->description,
        ]);

        return response([
            'status'  => true,
            'message' => 'Brand updated successfully',
            'data'    => $brand
        ], 200);
    }

    public function destroy($id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response([
                'message' => 'Not found',
                'status'  => 404,
            ], 404);
        }

        // حذف الصورة من السيرفر
        $imagePath = public_path('upload/' . basename($brand->logo));
        if ($brand->logo && file_exists($imagePath)) {
            unlink($imagePath);
        }

        $brand->delete();

        return response([
            'data'    => $brand,
            'message' => 'Deleted successfully',
            'status'  => 200,
        ], 200);
    }
}
