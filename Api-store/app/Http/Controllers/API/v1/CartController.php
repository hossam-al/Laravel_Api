<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $cart->load(['items.product:id,name,price']);
        return response()->json($cart);
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
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

        return DB::transaction(function () use ($cart, $data) {
            $item = CartItem::firstOrNew([
                'cart_id' => $cart->id,
                'product_id' => $data['product_id'],
            ]);
            $item->quantity = ($item->exists ? $item->quantity : 0) + $data['quantity'];
            $item->save();
            return response()->json($item->load('product:id,name,price'), 201);
        });
    }

    public function show(Cart $cart)
    {
        abort_if($cart->user_id !== auth()->id(), 403);
        return response()->json($cart->load(['items.product:id,name,price']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    public function update(Request $request, Cart $cart)
    {
        abort_if($cart->user_id !== auth()->id(), 403);
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $data['product_id'])
            ->firstOrFail();
        $item->update(['quantity' => $data['quantity']]);
        return response()->json($item);
    }

    public function destroy(Cart $cart)
    {
        abort_if($cart->user_id !== auth()->id(), 403);
        $cart->items()->delete();
        return response()->json(['message' => 'Cart cleared']);
    }
}
