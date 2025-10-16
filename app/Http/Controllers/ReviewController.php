<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class ReviewController extends Controller
{
    public function store(Request $request, $product)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);
            // Ensure user is authenticated
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $userId = Auth::id();

            // Check the user has at least one completed order that contains this product
            $hasBought = Order::where('user_id', $userId)
                ->where('status', 'completed')
                ->whereHas('orderItems', function($q) use ($product) {
                    $q->where('product_id', $product);
                })->exists();

            if (! $hasBought) {
                return back()->with('error', 'Bạn chỉ có thể đánh giá sản phẩm sau khi đơn hàng chứa sản phẩm này được xác nhận thành công.');
            }

            Review::create([
                'user_id' => $userId,
                'product_id' => $product,
                'rating' => $request->rating ?? 5,
                'comment' => $request->comment,
            ]);

            return back()->with('success', 'Cảm ơn bạn đã đánh giá!');
    }
}
