<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Super Admin can see all orders
        if ($user->role && $user->role->slug === 'super-admin') {
            $orders = Order::with('items')->latest()->get();
            return response()->json(['orders' => $orders]);
        }

        // Regular users can only see their own orders
        $orders = Order::where('user_id', $user->id)->with('items')->latest()->get();
        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found'], 404);
        }
        return response()->json([
            'message' => 'Orders retrieved successfully',
            'orders' => $orders
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);
        // Ensure product_id is provided and load the product safely
        if (! $request->filled('product_id')) {
            return response()->json(['message' => 'No product provided'], 400);
        }

        $findproduct = Products::find($request->product_id);
        if (! $findproduct) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $user = Auth::user();

        DB::beginTransaction();
        try {
            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'notes' => $request->notes,

                'order_number' => Str::upper(Str::random(10))
            ]);

            $totalAmount = 0;

            // Create order item for the single product
            $quantity = (int) $request->quantity;

            // Check stock availability
            if (isset($findproduct->stock) && $findproduct->stock < $quantity) {
                throw new \Exception("Product {$findproduct->name} does not have enough stock.");
            }

            $price = $findproduct->price ?? 0;
            $subtotal = $price * $quantity;
            $totalAmount = $subtotal;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $findproduct->id,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal
            ]);

            // Update product stock
            if (isset($findproduct->stock)) {
                $findproduct->stock = max(0, $findproduct->stock - $quantity);
                $findproduct->save();
            }

            // Update order total amount
            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order' => $order->load('items')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create order', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $order = Order::with('items.product')->findOrFail($id);

        // Check if user is authorized to view this order
        if (!($user->role && $user->role->slug === 'super-admin') && $order->user_id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json(['order' => $order]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $order = Order::findOrFail($id);

        // Only Super Admin or Admin can update order status
        if (!($user->role && ($user->role->slug === 'super-admin'))) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate request
        $request->validate([
            'status' => 'sometimes|in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        // Update order
        $order->update($request->only(['status', 'notes']));

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'order' => $order
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();


        if ($user->role && $user->role->slug !== 'super-admin') {
            return response()->json(['error' => 'super-admin only'], 403);
        }
        $order = Order::where('id', $id)->first();

        if (!$order) {
            return response()->json(['error' => 'order not found'], 404);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ]);
    }
    public function cancel(string $id)
    {
        $user = Auth::user();
        $order = Order::find($id);
        if ($order == null) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        // التحقق من الملكية
        if ($order->user_id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }


        // السماح بالإلغاء فقط للـ pending orders
        if ($order->status !== 'pending') {
            return response()->json([
                'error' => 'Cannot cancel order. Only pending orders can be cancelled.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // إرجاع Stock
            foreach ($order->items as $item) {
                $product = Products::find($item->product_id);
                if ($product && isset($product->stock)) {
                    $product->stock += $item->quantity;
                    $product->save();
                }
            }

            // تغيير Status
            $order->update(['status' => 'cancelled']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
                'order' => $order
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
