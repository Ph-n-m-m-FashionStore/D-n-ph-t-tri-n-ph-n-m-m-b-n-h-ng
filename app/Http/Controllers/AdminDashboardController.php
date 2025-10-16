<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function dashboard()
    {
        // Kiểm tra admin
        if (Auth::user() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $completedOrders = Order::where('status', 'completed')->with('orderItems');
        $totalRevenue = $completedOrders->get()->sum(function($o) { return $o->computed_total ?? ($o->total ?? 0); });

        $stats = [
            'total_revenue' => $totalRevenue,
            'total_orders' => Order::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_products' => Product::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'recent_orders' => Order::with('user')->latest()->take(5)->get(),
            'new_customers' => User::where('role', 'customer')->whereMonth('created_at', now()->month)->count(),
            'active_customers' => User::where('role', 'customer')->whereHas('orders', function($q){ $q->where('status', 'completed')->whereMonth('created_at', now()->month); })->count()
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function salesReport(Request $request)
    {
        if (Auth::user() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        // Logic thống kê doanh thu
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now());

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        // Xử lý dữ liệu báo cáo
        return view('admin.sales-report', compact('orders', 'startDate', 'endDate'));
    }
}