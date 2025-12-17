<?php

namespace App\Http\Controllers\API\v1;

use App\Models\products;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WishlistController extends Controller
{
    public function index()
    {
        $items = Wishlist::with('product:id,name,price')
            ->where('user_id', auth()->id())
            ->orderByDesc('id')
            ->paginate(10);
        return response()->json($items);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);
        $item = Wishlist::firstOrCreate([
            'user_id' => auth()->id(),
            'product_id' => $data['product_id'],
        ]);
        return response()->json($item->load('product:id,name,price'), 201);
    }

    public function show(Wishlist $wishlist)
    {
        abort_if($wishlist->user_id !== auth()->id(), 403);
        return response()->json($wishlist->load('product:id,name,price'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wishlist $wishlist)
    {
        //
    }

    public function update(Request $request, Wishlist $wishlist)
    {
        abort_if($wishlist->user_id !== auth()->id(), 403);
        // لا يوجد تحديث فعلي لقائمة الرغبات سوى ربما تعليق أو ملاحظة مستقبلية
        return response()->json($wishlist);
    }

    public function destroy(Wishlist $wishlist)
    {
        abort_if($wishlist->user_id !== auth()->id(), 403);
        $wishlist->delete();
        return response()->json(['message' => 'Wishlist item removed']);
    }
}
