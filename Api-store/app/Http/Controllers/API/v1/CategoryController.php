<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        if ($categories->isEmpty()) {
            $response = [
                'massege' => 'not data found',
                'status' => 404
            ];
        } else {
            $response = [
                'data' => $categories,
                'massege' => 'Get All data sussessfuly',
                'status' => 200,
            ];
        }
        return response($response, $response['status']);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            // slug مش محتاجه لأنه هيجيلك من الـ Observer
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified category.
     */
    public function show($id)
    {
        $category = Category::find($id);

        if ($category == null) {
            $response = [
                'massege' => 'not data found',
                'status' => 404
            ];
        } else {
            $response = [
                'data' => $category,
                'massege' => 'Get All data sussessfuly',
                'status' => 200,
            ];
        }
        return response($response, $response['status']);
    }
    public function showBySlug($slug)
    {
        $category = Category::where('slug', $slug)->first();
        if (!$category) {
            return response([
                'message' => 'Not found',
                'status'  => 404,
            ], 404);
        }
        return response()->json([
            'status' => true,
            'data' => $category
        ]);
    }
    /**
     * Update the specified category.
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response([
                'message' => 'Not found',
                'status'  => 404,
            ], 404);
        }
        $request->validate([
            'name' => 'required|string|unique:categories,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            // برضو slug هيتعامل معاه الـ Observer
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response([
                'message' => 'Not found',
                'status'  => 404,
            ], 404);
        }

        $category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully',
            'data' => $category
        ], 200);
    }
}
