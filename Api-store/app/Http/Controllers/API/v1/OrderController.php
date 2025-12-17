<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Order;
use App\Models\products;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Order::class);
        $orders = Order::with(['items.product:id,name'])
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($orders);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($validated) {
            $userId = auth()->id();

            $itemsPayload = collect($validated['items']);

            $productIds = $itemsPayload->pluck('product_id')->all();
            $productsMap = products::whereIn('id', $productIds)
                ->get(['id', 'price'])
                ->keyBy('id');

            $total = 0;
            foreach ($itemsPayload as $it) {
                $price = (float) ($productsMap[$it['product_id']]->price ?? 0);
                $total += $price * (int) $it['quantity'];
            }

            $order = Order::create([
                'user_id' => $userId,
                'total_price' => $total,
                'status' => 'pending',
            ]);

            foreach ($itemsPayload as $it) {
                $price = (float) ($productsMap[$it['product_id']]->price ?? 0);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $it['product_id'],
                    'quantity' => $it['quantity'],
                    'price' => $price,
                ]);
            }

            return response()->json($order->load('items'), 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        return response()->json($order->load(['items.product:id,name']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        $data = $request->validate([
            'status' => 'required|string|in:pending,paid,shipped,delivered,cancelled',
        ]);
        $order->update($data);
        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);
        $order->delete();
        return response()->json(['message' => 'Order deleted']);
    }
}
