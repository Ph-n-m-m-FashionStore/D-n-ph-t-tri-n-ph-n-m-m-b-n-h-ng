<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Báo cáo doanh thu tổng quan
     */
    public function sales(Request $request)
    {
        if (Auth::user() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        // Lấy tham số thời gian
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        // Validate dates
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Thống kê tổng quan
        $stats = $this->getSalesStats($startDate, $endDate);
        
        // Doanh thu theo ngày (cho biểu đồ)
        $dailyRevenue = $this->getDailyRevenue($startDate, $endDate);
        
        // Top sản phẩm bán chạy
        $topProducts = $this->getTopProducts($startDate, $endDate);
        
        // Đơn hàng gần đây
        $recentOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->with(['orderItems.product', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.reports.sales', compact(
            'stats', 
            'dailyRevenue', 
            'topProducts', 
            'recentOrders',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Báo cáo sản phẩm
     */
    public function products(Request $request)
    {
        if (Auth::user() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Sản phẩm bán chạy nhất
        $topSellingProducts = Product::withCount(['orderItems as total_sold' => function($query) use ($startDate, $endDate) {
                $query->whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed');
                });
            }])
            ->withSum(['orderItems as total_revenue' => function($query) use ($startDate, $endDate) {
                $query->whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed');
                });
            }], 'price')
            ->orderBy('total_sold', 'desc')
            ->take(20)
            ->get();

        // Sản phẩm không bán được
        $unsoldProducts = Product::whereDoesntHave('orderItems', function($query) use ($startDate, $endDate) {
                $query->whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed');
                });
            })
            ->where('is_active', 1)
            ->get();

        // Thống kê theo loại sản phẩm
    $productTypeStats = Product::select('product_type', 'id', 'name', 'price')
            ->withCount(['orderItems as total_sold' => function($query) use ($startDate, $endDate) {
                $query->whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed');
                });
            }])
            ->withSum(['orderItems as total_revenue' => function($query) use ($startDate, $endDate) {
                $query->whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed');
                });
            }], 'price')
            ->groupBy('product_type', 'id', 'name', 'price')
            ->get();

        return view('admin.reports.products', compact(
            'topSellingProducts',
            'unsoldProducts', 
            'productTypeStats',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Báo cáo khách hàng
     */
    public function customers(Request $request)
    {
        if (Auth::user() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Khách hàng mua nhiều nhất - compute from order->computed_total
        $topCustomers = User::where('role', 'customer')
            ->with(['orders' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('status', 'completed')
                  ->with('orderItems');
            }])
            ->get()
            ->map(function($u) {
                $total = $u->orders->sum(function($o){ return $o->computed_total ?? ($o->total ?? 0); });
                $u->computed_report_total = $total;
                $u->computed_orders_count = $u->orders->count();
                return $u;
            })
            ->filter(function($u){ return $u->computed_orders_count > 0; })
            ->sortByDesc('computed_report_total')
            ->take(20)
            ->values();

        // Khách hàng mới
        $newCustomers = User::where('role', 'customer')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total')
            ->latest()
            ->get();

        // Thống kê khách hàng using computed totals
        $totalCustomers = User::where('role', 'customer')->count();
        $activeCustomers = User::where('role', 'customer')
            ->whereHas('orders', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('status', 'completed');
            })->count();

        // Compute avg order value from computed_total
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->with('orderItems')
            ->get();
        $totalRevenue = $orders->sum(function($o){ return $o->computed_total ?? ($o->total ?? 0); });
        $avgOrderValue = $orders->count() ? ($totalRevenue / $orders->count()) : 0;

        $customerStats = [
            'total_customers' => $totalCustomers,
            'new_customers' => $newCustomers->count(),
            'active_customers' => $activeCustomers,
            'avg_order_value' => $avgOrderValue
        ];

        return view('admin.reports.customers', compact(
            'topCustomers',
            'newCustomers',
            'customerStats',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Dashboard tổng quan
     */
    public function dashboard()
    {
        if (Auth::user() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $today = now();
        $thisMonth = $today->copy()->startOfMonth();
        $lastMonth = $today->copy()->subMonth()->startOfMonth();
        $thisMonthEnd = $today->copy()->endOfMonth();
        $lastMonthEnd = $today->copy()->subMonth()->endOfMonth();

        // Thống kê tổng quan
        // Use computed_total on orders to stay consistent with order detail computation
        $allCompletedOrders = Order::where('status', 'completed')->with('orderItems');
        $stats = [
            'total_revenue' => $allCompletedOrders->get()->sum(function($o) { return $o->computed_total ?? ($o->total ?? 0); }),
            'monthly_revenue' => Order::where('status', 'completed')
                ->whereBetween('created_at', [$thisMonth, $thisMonthEnd])
                ->with('orderItems')
                ->get()
                ->sum(function($o){ return $o->computed_total ?? ($o->total ?? 0); }),
            'last_month_revenue' => Order::where('status', 'completed')
                ->whereBetween('created_at', [$lastMonth, $lastMonthEnd])
                ->with('orderItems')
                ->get()
                ->sum(function($o){ return $o->computed_total ?? ($o->total ?? 0); }),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', 1)->count(),
        ];

        // Tính % tăng trưởng
        $revenueGrowth = 0;
        if ($stats['last_month_revenue'] > 0) {
            $revenueGrowth = (($stats['monthly_revenue'] - $stats['last_month_revenue']) / $stats['last_month_revenue']) * 100;
        }

        // Doanh thu 7 ngày gần đây
        $dailyRevenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $ordersOnDate = Order::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->with('orderItems')
                ->get();
            $revenue = $ordersOnDate->sum(function($o) { return $o->computed_total ?? ($o->total ?? 0); });
            $dailyRevenue[] = [
                'date' => $date->format('Y-m-d'),
                'revenue' => $revenue
            ];
        }

        // Top sản phẩm bán chạy tháng này
        $topProducts = Product::withCount(['orderItems as total_sold' => function($query) use ($thisMonth, $thisMonthEnd) {
                $query->whereHas('order', function($q) use ($thisMonth, $thisMonthEnd) {
                    $q->whereBetween('created_at', [$thisMonth, $thisMonthEnd])
                      ->where('status', 'completed');
                });
            }])
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        // Đơn hàng gần đây
        $recentOrders = Order::with(['user', 'orderItems.product'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'revenueGrowth',
            'dailyRevenue',
            'topProducts',
            'recentOrders'
        ));
    }

    // ==================== PRIVATE HELPER METHODS ====================

    private function getSalesStats($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->with('orderItems')
            ->get();

        $totalRevenue = $orders->sum(function($o) { return $o->computed_total ?? ($o->total ?? 0); });
        $totalOrders = $orders->count();
        $avgOrderValue = $totalOrders ? ($totalRevenue / $totalOrders) : 0;
        $totalCustomers = $orders->unique('user_id')->count();

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'avg_order_value' => $avgOrderValue,
            'total_customers' => $totalCustomers
        ];
    }

    private function getDailyRevenue($startDate, $endDate)
    {
        $days = [];
        $cursor = Carbon::parse($startDate->format('Y-m-d'));
        $end = Carbon::parse($endDate->format('Y-m-d'));
        while ($cursor->lte($end)) {
            $ordersOnDate = Order::whereDate('created_at', $cursor)
                ->where('status', 'completed')
                ->with('orderItems')
                ->get();
            $revenue = $ordersOnDate->sum(function($o) { return $o->computed_total ?? ($o->total ?? 0); });
            $days[] = [
                'date' => $cursor->format('Y-m-d'),
                'revenue' => $revenue
            ];
            $cursor->addDay();
        }
        return collect($days);
    }

    private function getTopProducts($startDate, $endDate)
    {
        return Product::withCount(['orderItems as total_sold' => function($query) use ($startDate, $endDate) {
                $query->whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed');
                });
            }])
            ->withSum(['orderItems as total_revenue' => function($query) use ($startDate, $endDate) {
                $query->whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed');
                });
            }], 'price')
            ->orderBy('total_sold', 'desc')
            ->take(10)
            ->get();
    }
}
