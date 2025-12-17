<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Cart;
use App\Models\User;
use App\Models\Brand;
use App\Models\Order;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\products;
use App\Models\Wishlist;
use App\Models\RequestLog;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $range = request('range', '7d'); // 24h, 7d, 30d, all
        $from = null;
        if ($range !== 'all') {
            $now = now();
            $from = match ($range) {
                '24h' => $now->copy()->subDay(),
                '30d' => $now->copy()->subDays(30),
                default => $now->copy()->subDays(7),
            };
        }

        $metrics = [
            'users' => User::count(),
            'products' => products::count(),
            'orders' => Order::count(),
            'categories' => Category::count(),
            'brands' => Brand::count(),
        ];

        $recentLogsQuery = RequestLog::query();
        if ($from) { $recentLogsQuery->where('created_at', '>=', $from); }
        $recentLogs = $recentLogsQuery->orderByDesc('created_at')
            ->limit(20)
            ->get(['method', 'path', 'status_code', 'ip', 'created_at']);

        $topEndpointsQuery = RequestLog::selectRaw('method, path, COUNT(*) as hits');
        if ($from) { $topEndpointsQuery->where('created_at', '>=', $from); }
        $topEndpoints = $topEndpointsQuery
            ->groupBy('method', 'path')
            ->orderByDesc('hits')
            ->limit(10)
            ->get();

        $recentOrdersQuery = Order::with(['user:id,name']);
        if ($from) { $recentOrdersQuery->where('created_at', '>=', $from); }
        $recentOrders = $recentOrdersQuery
            ->withCount('items')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['id', 'user_id', 'total_price', 'status', 'created_at']);

        $orderStatusBreakdownQuery = Order::selectRaw('status, COUNT(*) as count');
        if ($from) { $orderStatusBreakdownQuery->where('created_at', '>=', $from); }
        $orderStatusBreakdown = $orderStatusBreakdownQuery
            ->groupBy('status')
            ->orderByDesc(DB::raw('COUNT(*)'))
            ->get();

        $revenueQuery = Order::query();
        if ($from) { $revenueQuery->where('created_at', '>=', $from); }
        $revenue = (float) $revenueQuery->sum('total_price');
        $ordersCount = (int) $revenueQuery->count();
        $avgOrderValue = $ordersCount > 0 ? $revenue / $ordersCount : 0.0;

        $topCartProducts = CartItem::selectRaw('product_id, COUNT(*) as times, SUM(quantity) as qty')
            ->when($from, function ($q) use ($from) {
                $q->where('created_at', '>=', $from);
            })
            ->groupBy('product_id')
            ->orderByDesc('times')
            ->limit(10)
            ->get();

        $topWishlistProducts = Wishlist::selectRaw('product_id, COUNT(*) as times')
            ->when($from, function ($q) use ($from) {
                $q->where('created_at', '>=', $from);
            })
            ->groupBy('product_id')
            ->orderByDesc('times')
            ->limit(10)
            ->get();

        $cartMetrics = [
            'carts' => Cart::count(),
            'cart_items' => CartItem::count(),
            'wishlists' => Wishlist::count(),
            'revenue' => $revenue,
            'avg_order_value' => $avgOrderValue,
            'range' => $range,
        ];

        return view('admin.dashboard', compact(
            'metrics',
            'recentLogs',
            'topEndpoints',
            'recentOrders',
            'orderStatusBreakdown',
            'cartMetrics',
            'topCartProducts',
            'topWishlistProducts'
        ));
    }
}


