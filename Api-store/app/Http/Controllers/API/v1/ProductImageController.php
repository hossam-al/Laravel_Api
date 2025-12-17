<?php

namespace App\Http\Controllers\API\v1;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProductImageController extends Controller
{
    public function index()
    {
        $ProductImage = ProductImage::with('product')->get();
        if ($ProductImage->isEmpty()) {
            $response = [
                'message' => 'not data found',
                'status' => 404
            ];
        } else {
            $response = [
                'data' => $ProductImage,
                'message' => 'Get All data sussessfuly',
                'status' => 200,
            ];
        }
        return response($response, $response['status']);
    }


    public function store(Request $request)
    {


        $mega = 2 * 1024 * 1024; // 1 MB
        $request->validate([
            'product_id' => 'required|exists:products,id',   // لازم يكون فيه منتج موجود
            'image' => "required|file|max:$mega",
            'is_primary' => 'boolean',                      // 0 أو 1 (افتراضي false)
        ]);

        if ($request->hasFile('image')) {
            $image_data = $request->file('image');
            $image_name = time() . $image_data->getClientOriginalName();
            $location = public_path('upload'); // Corrected variable name
            $image_data->move($location, $image_name);
            $image_path = url('upload/' . $image_name); // Generate the full URL for the image
        } else {
            $image_name = null;
            $image_path = null;
        }



        $ProductImage = ProductImage::create([
            'product_id' => $request->product_id,
            'image' => $image_path,
            'is_primary' => $request->is_primary
        ]);

        $response = [
            'data' => $ProductImage,
            'message' => 'Get All data sussessfuly',
            'status' => 201,
        ];
        return response($response, $response['status']);
    }


    public function show($id)
    {
        $ProductImage = ProductImage::find($id);

        if ($ProductImage == null) {
            $response = [
                'message' => 'not data found',
                'status' => 404
            ];
        } else {
            $response = [
                'data' => $ProductImage,
                'message' => 'Add data sussessfuly',
                'status' => 200,
            ];
        }
        return response($response, $response['status']);
    }

    public function update(Request $request, $id)
    {
        $mega = 2 * 1024 * 1024; // 2 MB in bytes
        $request->validate([
            'product_id' => 'required|exists:products,id', // Product must exist
            'image' => "nullable|file|max:$mega",
            'is_primary' => 'boolean', // 0 or 1 (default false)
        ]);

        $ProductImage = ProductImage::find($id);

        if ($ProductImage == null) {
            $response = [
                'message' => 'ProductImage not found!',
                'status' => 404,
            ];
        } else {
            if ($request->hasFile('image')) {
                $imagePath = public_path('upload/' . basename($ProductImage->image));
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }

                $image_data = $request->file('image');
                $image_name = time() . $image_data->getClientOriginalName();
                $location = public_path('upload');
                $image_data->move($location, $image_name);
                $image_path = url('upload/' . $image_name);
            } else {
                $image_path = $ProductImage->image;
            }

            $ProductImage->update([
                'product_id' => $request->product_id,
                'image' => $image_path,
                'is_primary' => $request->is_primary,
            ]);

            $response = [
                'data' => $ProductImage,
                'message' => 'Data updated successfully',
                'status' => 200,
            ];
        }

        return response($response, $response['status']);
    }

    public function destroy($id)
    {
        $ProductImage = ProductImage::find($id);
        if ($ProductImage == null) {
            $response = [

                'meesage' => 'not found data!',
                'status' => 404,
            ];
        } else {

            $imagePath = public_path('upload/' . basename($ProductImage['image']));
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $ProductImage->delete();
            $response = [
                'data' => $ProductImage,
                'meesage' => 'delete data successfuly',
                'status' => 200,
            ];
        }


        return response($response, $response['status']);
    }
}
