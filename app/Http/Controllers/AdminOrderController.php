<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrderController extends Controller
{
    public function index()
    {
        if (Auth::user() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $orders = Order::with(['user', 'orderItems.product', 'payment'])
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if (Auth::user() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $order->load(['user', 'orderItems.product', 'payment']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        if (Auth::user() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,shipping,completed,canceled'
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    public function confirmPayment(Order $order)
    {
        if (Auth::user() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        // Tạo hoặc cập nhật payment
        if ($order->payment) {
            $order->payment->update(['status' => 'paid']);
        } else {
            // compute amount from items to ensure consistency with displayed detail
            $itemsTotal = $order->orderItems->sum(function($it) { return ($it->price ?? 0) * ($it->quantity ?? 0); });
            $shipping = $order->shipping_fee ?? $order->shipping ?? 0;
            $discount = $order->discount ?? 0;
            $tax = $order->tax ?? 0;
            $amount = $itemsTotal + $shipping - $discount + $tax;

            Payment::create([
                'order_id' => $order->id,
                'method' => $order->payment_type ?? 'bank-transfer',
                'amount' => $amount,
                'status' => 'paid'
            ]);
        }

        return redirect()->back()->with('success', 'Xác nhận thanh toán thành công!');
    }
}