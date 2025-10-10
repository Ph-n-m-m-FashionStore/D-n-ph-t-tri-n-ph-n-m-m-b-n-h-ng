<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderStatusController extends Controller
{
    // Hiển thị trạng thái đơn hàng
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())->get();
        return view('order_status.index', compact('orders'));
    }

    // Cập nhật trạng thái đơn hàng (chỉ admin hoặc qua API)
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();
        return redirect()->route('order-status.index')->with('success', 'Cập nhật trạng thái thành công!');
    }
}
