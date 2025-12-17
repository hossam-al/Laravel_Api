<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Order;
use App\Models\products;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class OrderItemController extends Controller
{
    public function index(Order $order)
    {
        $this->authorize('view', $order);
        $items = $order->items()->with('product:id,name')->orderByDesc('id')->get();
        return response()->json($items);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = products::findOrFail($data['product_id']);

        return DB::transaction(function () use ($order, $data, $product) {
            $item = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
                'price' => (float) $product->price,
            ]);

            $this->recalculateOrderTotal($order);

            return response()->json($item, 201);
        });
    }

    public function show(Order $order, OrderItem $orderItem)
    {
        $this->authorize('view', $order);
        abort_if($orderItem->order_id !== $order->id, 404);
        return response()->json($orderItem->load('product:id,name'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderItem $orderItem)
    {
        //
    }

    public function update(Request $request, Order $order, OrderItem $orderItem)
    {
        $this->authorize('update', $order);
        abort_if($orderItem->order_id !== $order->id, 404);

        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($order, $orderItem, $data) {
            $orderItem->update(['quantity' => $data['quantity']]);
            $this->recalculateOrderTotal($order);
            return response()->json($orderItem);
        });
    }

    public function destroy(Order $order, OrderItem $orderItem)
    {
        $this->authorize('delete', $order);
        abort_if($orderItem->order_id !== $order->id, 404);

        return DB::transaction(function () use ($order, $orderItem) {
            $orderItem->delete();
            $this->recalculateOrderTotal($order);
            return response()->json(['message' => 'Order item deleted']);
        });
    }

    private function authorizeOrder(Order $order): void {}

    private function recalculateOrderTotal(Order $order): void
    {
        $freshTotal = (float) $order->items()->sum(DB::raw('quantity * price'));
        $order->update(['total_price' => $freshTotal]);
    }
}
