<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Http\Requests\CustomerRequest;

class CustomerController extends Controller
{
    /**
     * Danh sách khách hàng cho admin
     */
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $query = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total');

        // Tìm kiếm theo tên, email, phone
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Lọc theo khoảng thời gian đăng ký
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Lọc theo số đơn hàng
        if ($request->has('min_orders') && $request->min_orders) {
            $query->having('orders_count', '>=', $request->min_orders);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if ($sortBy === 'total_spent') {
            $query->orderBy('total_spent', $sortOrder);
        } elseif ($sortBy === 'orders_count') {
            $query->orderBy('orders_count', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $customers = $query->paginate(20);

        // Thống kê tổng quan
        $stats = [
            'total_customers' => User::where('role', 'customer')->count(),
            'new_customers' => User::where('role', 'customer')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'active_customers' => User::where('role', 'customer')
                ->whereHas('orders', function($q) {
                    $q->where('created_at', '>=', now()->subDays(30));
                })
                ->count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total')
        ];

        return view('admin.customers.index', compact('customers', 'stats'));
    }

    /**
     * Chi tiết khách hàng
     */
    public function show(User $customer)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        if ($customer->role === 'admin') {
            abort(404);
        }

        // Load thông tin khách hàng với các quan hệ
        $customer->load([
            'orders' => function($query) {
                $query->with(['orderItems.product', 'payment'])
                      ->latest();
            }
        ]);

        // Thống kê khách hàng
        $customerStats = [
            'total_orders' => $customer->orders->count(),
            'total_spent' => $customer->orders->where('status', 'completed')->sum('total'),
            'avg_order_value' => $customer->orders->where('status', 'completed')->avg('total'),
            'last_order_date' => $customer->orders->first()?->created_at,
            'orders_by_status' => $customer->orders->groupBy('status')->map->count()
        ];

        // Đơn hàng gần đây (5 đơn cuối)
        $recentOrders = $customer->orders->take(5);

        return view('admin.customers.show', compact('customer', 'customerStats', 'recentOrders'));
    }

    /**
     * Cập nhật thông tin khách hàng
     */
    public function update(CustomerRequest $request, User $customer)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        if ($customer->role === 'admin') {
            abort(404);
        }

        $data = $request->only(['name', 'email', 'phone', 'address']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $customer->update($data);

        return redirect()->back()->with('success', 'Thông tin khách hàng đã được cập nhật!');
    }

    /**
     * Vô hiệu hóa/kích hoạt tài khoản khách hàng
     */
    public function toggleStatus(User $customer)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        if ($customer->role === 'admin') {
            abort(404);
        }

        $customer->update(['is_active' => !$customer->is_active]);
        
        $status = $customer->is_active ? 'kích hoạt' : 'vô hiệu hóa';
        return redirect()->back()
            ->with('success', "Tài khoản khách hàng đã được {$status} thành công!");
    }

    /**
     * Xóa tài khoản khách hàng (chỉ khi chưa có đơn hàng)
     */
    public function destroy(User $customer)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        if ($customer->role === 'admin') {
            abort(404);
        }

        // Kiểm tra xem khách hàng có đơn hàng không
        if ($customer->orders()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Không thể xóa khách hàng đã có đơn hàng!');
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Khách hàng đã được xóa thành công!');
    }

    /**
     * Lịch sử mua hàng của khách hàng
     */
    public function orderHistory(User $customer, Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        if ($customer->role === 'admin') {
            abort(404);
        }

        $query = $customer->orders()->with(['orderItems.product', 'payment']);

        // Lọc theo trạng thái
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Lọc theo khoảng thời gian
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.customers.order-history', compact('customer', 'orders'));
    }
}
