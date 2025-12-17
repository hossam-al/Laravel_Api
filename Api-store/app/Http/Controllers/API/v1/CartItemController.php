<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CartItemController extends Controller
{
    public function index(Cart $cart)
    {
        abort_if($cart->user_id !== auth()->id(), 403);
        $items = $cart->items()->with('product:id,name,price')->orderByDesc('id')->get();
        return response()->json($items);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request, Cart $cart)
    {
        abort_if($cart->user_id !== auth()->id(), 403);
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

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

    public function show(Cart $cart, CartItem $cartItem)
    {
        abort_if($cart->user_id !== auth()->id(), 403);
        abort_if($cartItem->cart_id !== $cart->id, 404);
        return response()->json($cartItem->load('product:id,name,price'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CartItem $cartItem)
    {
        //
    }

    public function update(Request $request, Cart $cart, CartItem $cartItem)
    {
        abort_if($cart->user_id !== auth()->id(), 403);
        abort_if($cartItem->cart_id !== $cart->id, 404);
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $cartItem->update(['quantity' => $data['quantity']]);
        return response()->json($cartItem);
    }

    public function destroy(Cart $cart, CartItem $cartItem)
    {
        abort_if($cart->user_id !== auth()->id(), 403);
        abort_if($cartItem->cart_id !== $cart->id, 404);
        $cartItem->delete();
        return response()->json(['message' => 'Cart item deleted']);
    }
}
