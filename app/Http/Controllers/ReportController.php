<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Báo cáo doanh thu tổng quan
     */
    public function sales(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
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
        if (auth()->user()->role !== 'admin') {
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
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Khách hàng mua nhiều nhất
        $topCustomers = User::where('role', 'customer')
            ->withCount(['orders as total_orders' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed');
            }])
            ->withSum(['orders as total_spent' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed');
            }], 'total')
            ->having('total_orders', '>', 0)
            ->orderBy('total_spent', 'desc')
            ->take(20)
            ->get();

        // Khách hàng mới
        $newCustomers = User::where('role', 'customer')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total')
            ->latest()
            ->get();

        // Thống kê khách hàng
        $customerStats = [
            'total_customers' => User::where('role', 'customer')->count(),
            'new_customers' => $newCustomers->count(),
            'active_customers' => User::where('role', 'customer')
                ->whereHas('orders', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed');
                })
                ->count(),
            'avg_order_value' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->avg('total')
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
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $today = now();
        $thisMonth = $today->copy()->startOfMonth();
        $lastMonth = $today->copy()->subMonth()->startOfMonth();
        $thisMonthEnd = $today->copy()->endOfMonth();
        $lastMonthEnd = $today->copy()->subMonth()->endOfMonth();

        // Thống kê tổng quan
        $stats = [
            'total_revenue' => Order::where('status', 'completed')->sum('total'),
            'monthly_revenue' => Order::where('status', 'completed')
                ->whereBetween('created_at', [$thisMonth, $thisMonthEnd])
                ->sum('total'),
            'last_month_revenue' => Order::where('status', 'completed')
                ->whereBetween('created_at', [$lastMonth, $lastMonthEnd])
                ->sum('total'),
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
            $revenue = Order::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('total');
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
            ->where('status', 'completed');

        return [
            'total_revenue' => $orders->sum('total'),
            'total_orders' => $orders->count(),
            'avg_order_value' => $orders->avg('total'),
            'total_customers' => $orders->distinct('user_id')->count()
        ];
    }

    private function getDailyRevenue($startDate, $endDate)
    {
        $dailyRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $dailyRevenue;
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
