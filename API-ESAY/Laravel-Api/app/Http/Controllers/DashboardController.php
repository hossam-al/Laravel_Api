<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Role;
use App\Models\products;
use App\Models\category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function showOwnerLoginForm()
    {
        // If user is already logged in and is owner, redirect to dashboard
        if (Auth::check() && Auth::user()->role && Auth::user()->role->slug === 'super-admin') {
            return redirect('/dashboard/owner');
        }

        return view('dashboard.login');
    }

    public function ownerLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $role = Role::find($user->role_id);

            // Check if user is superadmin/owner
            if ($role && $role->slug === 'super-admin') {
                // Generate a session for the dashboard
                $request->session()->regenerate();

                return redirect('/dashboard/owner');
            } else {
                Auth::logout();
                return back()->with('error', 'غير مصرح لك بالوصول إلى لوحة التحكم');
            }
        }

        return back()->with('error', 'بيانات تسجيل الدخول غير صحيحة');
    }

    public function ownerLogout(Request $request)
    {
        // Logout user
        Auth::logout();

        // Invalidate session and regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/dashboard/login')->with('success', 'تم تسجيل الخروج بنجاح');
    }

    public function ownerDashboard()
    {
        // Check if user is authenticated and has owner/superadmin role
        if (!Auth::check()) {
            return redirect('/dashboard/login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $user = Auth::user();
        $role = Role::find($user->role_id);

        // Check if user is superadmin/owner
        if (!$role || $role->slug !== 'super-admin') {
            return redirect('/')->with('error', 'غير مصرح لك بالوصول إلى لوحة التحكم');
        }

        // Get dashboard statistics
        $stats = [
            'orders_count' => Order::count(),
            'products_count' => products::count(),
            'users_count' => User::count(),
            'categories_count' => category::count(),
            'orders_growth' => $this->getGrowthPercentage(Order::class),
            'products_growth' => $this->getGrowthPercentage(products::class),
            'users_growth' => $this->getGrowthPercentage(User::class),
        ];

        // Get recent orders
        $recent_orders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get recent users
        $recent_users = User::with('role')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        // Get recent products
        $recent_products = products::with('category')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        return view('dashboard.owner', compact(
            'stats',
            'recent_orders',
            'recent_users',
            'recent_products',
            'user'
        ));
    }

    /**
     * Calculate growth percentage compared to previous month
     */
    private function getGrowthPercentage($model)
    {
        $now = now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // Get previous month and year
        $previousMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
        $previousYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;

        // Count records for current and previous month
        $currentCount = $model::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $previousCount = $model::whereMonth('created_at', $previousMonth)
            ->whereYear('created_at', $previousYear)
            ->count();

        // Calculate growth percentage
        if ($previousCount == 0) {
            return $currentCount > 0 ? 100 : 0; // If previous was 0, show 100% growth or 0
        }

        return round((($currentCount - $previousCount) / $previousCount) * 100);
    }
}
