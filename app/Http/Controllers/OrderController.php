<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Http\Requests\OrderStatusRequest;

class OrderController extends Controller
{
    // ==================== FRONTEND METHODS (Cho khách hàng) ====================
    
    public function show($id)
    {
        $user = auth()->user();
        
        // Admin có thể xem tất cả đơn hàng
        if ($user->role === 'admin') {
            $order = Order::with(['orderItems.product', 'user', 'payment'])
                        ->where('id', $id)
                        ->firstOrFail();
        } else {
            // User thường chỉ xem đơn của mình
            $order = Order::with(['orderItems.product', 'payment'])
                        ->where('id', $id)
                        ->where('user_id', $user->id)
                        ->firstOrFail();
        }
        
        return view('orders.show', compact('order'));
    }

    public function index()
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem đơn hàng.');
        }

        // Admin: danh sách đơn hàng
        if ($user->role === 'admin') {
            $orders = Order::with(['user', 'orderItems.product', 'payment'])
                         ->latest()
                         ->paginate(20);
            $statusCounts = [
                'all' => Order::count(),
                'pending' => Order::where('status', 'pending')->count(),
                'confirmed' => Order::where('status', 'confirmed')->count(),
                'shipping' => Order::where('status', 'shipping')->count(),
                'completed' => Order::where('status', 'completed')->count(),
                'canceled' => Order::where('status', 'canceled')->count(),
            ];
            return view('admin.orders.index', compact('orders', 'statusCounts'));
        }

        // User thường: danh sách đơn hàng với trạng thái để theo dõi
        $orders = Order::where('user_id', $user->id)
                       ->with(['orderItems.product', 'payment'])
                       ->latest()
                       ->get();

        $ordersWithNullTotal = $orders->whereNull('total')->count();

        return view('orders.index', compact('orders', 'ordersWithNullTotal'));
    }

    /**
     * Trả về JSON trạng thái đơn hàng của user để client polling.
     */
    public function statuses(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $orders = Order::where('user_id', $user->id)
                       ->latest()
                       ->get(['id', 'status', 'updated_at']);

        return response()->json([
            'orders' => $orders->map(function ($o) {
                return [
                    'id' => $o->id,
                    'status' => $o->status,
                    'updated_at' => optional($o->updated_at)->toIso8601String(),
                ];
            })
        ]);
    }

    /**
     * Màn hình chờ xác nhận thanh toán (user nhìn thấy sau khi bấm thanh toán).
     */
    public function waiting(Order $order)
    {
        $user = auth()->user();
        if (!$user || $order->user_id !== $user->id) {
            abort(403);
        }
        $order->load('payment');
        return view('orders.waiting', compact('order'));
    }

    /**
     * API trả về trạng thái thanh toán hiện tại của đơn.
     */
    public function status(Order $order)
    {
        $user = auth()->user();
        if (!$user || $order->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $order->load('payment');
        return response()->json([
            'status' => $order->status,
            'payment_status' => optional($order->payment)->status,
        ]);
    }

    // Hủy đơn hàng (chỉ cho user)
    public function cancel(Request $request, $id)
    {
        $user = auth()->user();
        $order = Order::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        // Chỉ cho phép hủy đơn hàng ở trạng thái pending
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ có thể hủy đơn hàng đang chờ xử lý.');
        }

        $order->update(['status' => 'canceled']);

        return redirect()->route('orders.index')->with('success', 'Đơn hàng đã được hủy thành công.');
    }

    // Theo dõi đơn hàng (public - không cần login)
    public function track(Request $request)
    {
        $orderCode = $request->get('order_code');
        $phone = $request->get('phone');
        
        $order = null;
        
        if ($orderCode && $phone) {
            $order = Order::with(['orderItems.product', 'user'])
                        ->where('id', $orderCode)
                        ->where(function($query) use ($phone) {
                            $query->where('phone', $phone)
                                  ->orWhereHas('user', function($q) use ($phone) {
                                      $q->where('phone', $phone);
                                  });
                        })
                        ->first();
        }

        return view('orders.track', compact('order', 'orderCode', 'phone'));
    }

    // ==================== ADMIN METHODS ====================

    /**
     * Danh sách đơn hàng cho admin (với tìm kiếm và lọc)
     */
    public function adminIndex(Request $request)
    {
        // Kiểm tra quyền admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $query = Order::with(['user', 'orderItems.product', 'payment']);
        
        // Tìm kiếm theo mã đơn, tên, email, phone
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Lọc theo trạng thái
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Lọc theo trạng thái thanh toán
        if ($request->has('payment_status') && $request->payment_status) {
            $query->whereHas('payment', function($q) use ($request) {
                $q->where('status', $request->payment_status);
            });
        }

        $orders = $query->latest()->paginate(20);
        
        // Thống kê số lượng theo trạng thái
        $statusCounts = [
            'all' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'shipping' => Order::where('status', 'shipping')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'canceled' => Order::where('status', 'canceled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    /**
     * Chi tiết đơn hàng cho admin
     */
    public function adminShow(Order $order)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $order->load(['user', 'orderItems.product', 'payment']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Cập nhật trạng thái đơn hàng (Admin)
     */
    public function updateStatus(OrderStatusRequest $request, Order $order)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        // Xử lý business logic khi thay đổi trạng thái
        $this->handleStatusChange($order, $oldStatus, $request->status);

        return redirect()->back()->with('success', 'Trạng thái đơn hàng đã được cập nhật!');
    }

    /**
     * Xác nhận thanh toán (Admin)
     */
    public function confirmPayment(Order $order)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        // Tạo hoặc cập nhật payment
        if ($order->payment) {
            $order->payment->update(['status' => 'paid']);
        } else {
            Payment::create([
                'order_id' => $order->id,
                'method' => $order->payment_type ?? 'COD',
                'amount' => $order->total,
                'status' => 'paid'
            ]);
        }

        // Nếu đơn hàng đang pending, tự động chuyển sang confirmed
        if ($order->status === 'pending') {
            $order->update(['status' => 'confirmed']);
        }

        return redirect()->back()->with('success', 'Đã xác nhận thanh toán thành công!');
    }

    /**
     * Cập nhật thông tin đơn hàng (Admin)
     */
    public function updateOrderInfo(Request $request, Order $order)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'note' => 'nullable|string|max:1000'
        ]);

        $order->update($request->only(['name', 'phone', 'address', 'note']));

        return redirect()->back()->with('success', 'Thông tin đơn hàng đã được cập nhật!');
    }

    /**
     * Xóa đơn hàng (Admin - chỉ cho phép xóa đơn đã hủy)
     */
    public function destroy(Order $order)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        // Chỉ cho phép xóa đơn hàng ở trạng thái canceled
        if ($order->status !== 'canceled') {
            return redirect()->back()->with('error', 'Chỉ có thể xóa đơn hàng đã hủy!');
        }

        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Đơn hàng đã được xóa!');
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Xử lý business logic khi thay đổi trạng thái đơn hàng
     */
    private function handleStatusChange(Order $order, $oldStatus, $newStatus)
    {
        // Nếu hủy đơn hàng, trả lại số lượng sản phẩm
        if ($newStatus === 'canceled' && $oldStatus !== 'canceled') {
            foreach ($order->orderItems as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }
        }

        // Nếu từ canceled chuyển sang trạng thái khác, trừ lại stock
        if ($oldStatus === 'canceled' && $newStatus !== 'canceled') {
            foreach ($order->orderItems as $item) {
                if ($item->product && $item->product->stock >= $item->quantity) {
                    $item->product->decrement('stock', $item->quantity);
                }
            }
        }

        // Tự động cập nhật payment status khi đơn hàng completed
        if ($newStatus === 'completed' && $order->payment) {
            $order->payment->update(['status' => 'paid']);
        }

        // TODO: Gửi thông báo/email cho khách hàng khi trạng thái thay đổi
        // $this->sendStatusUpdateNotification($order, $oldStatus, $newStatus);
    }

    /**
     * Gửi thông báo khi trạng thái đơn hàng thay đổi (có thể triển khai sau)
     */
    private function sendStatusUpdateNotification(Order $order, $oldStatus, $newStatus)
    {
        // Triển khai gửi email/thông báo cho khách hàng
        // if ($order->user && $order->user->email) {
        //     // Gửi email thông báo
        // }
    }
}