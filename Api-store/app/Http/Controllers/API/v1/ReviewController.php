<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with(['product:id,name'])
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($reviews);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = Review::create([
            'user_id' => auth()->id(),
            'product_id' => $data['product_id'],
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return response()->json($review, 201);
    }

    public function show(Review $review)
    {
        $this->authorizeReview($review);
        return response()->json($review->load('product:id,name'));
    }

    public function update(Request $request, Review $review)
    {
        $this->authorizeReview($review);
        $data = $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);
        $review->update($data);
        return response()->json($review);
    }

    public function destroy(Review $review)
    {
        $this->authorizeReview($review);
        $review->delete();
        return response()->json(['message' => 'Review deleted']);
    }

    private function authorizeReview(Review $review): void
    {
        abort_if($review->user_id !== auth()->id(), 403, 'Not allowed');
    }
}
