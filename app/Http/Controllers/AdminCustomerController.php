<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCustomerController extends Controller
{
    public function index()
    {
        if (Auth::user() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $customers = User::where('role', 'customer')
            ->withCount('orders')
            // Eager load completed orders with order items so computed_total accessor can sum without N+1
            ->with(['orders' => function($q) {
                $q->where('status', 'completed')->with('orderItems');
            }])
            ->latest()
            ->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        if (Auth::user() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $customer->load(['orders' => function($query) {
            $query->with('orderItems.product')->latest();
        }]);

        return view('admin.customers.show', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        if (Auth::user() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        $customer->update($request->all());

        return redirect()->back()->with('success', 'Cập nhật thông tin khách hàng thành công!');
    }
}