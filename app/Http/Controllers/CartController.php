<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function showCheckoutForm()
    {
    /** @var \App\Models\User|null $user */
    $user = Auth::user();
        if (!$user || !$user->cart) {
            return redirect()->route('cart.index')->with('error', 'Bạn cần có sản phẩm trong giỏ hàng để thanh toán.');
        }
        $cartItems = $user->cart->items()->with('product')->get();
        $total = $cartItems->sum(function($item){ return $item->product->price * $item->quantity; });
        return view('cart.checkout', compact('cartItems', 'total', 'user'));
    }

    public function processCheckout(Request $request)
    {
    /** @var \App\Models\User|null $user */
    $user = Auth::user();
        if (!$user || !$user->cart) {
            return redirect()->route('cart.index')->with('error', 'Bạn cần có sản phẩm trong giỏ hàng để thanh toán.');
        }
        $cartItems = $user->cart->items()->with('product')->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống.');
        }
        $total = $cartItems->sum(function($item){ return $item->product->price * $item->quantity; });

        // Validate thông tin
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'payment_type' => 'required|in:cod,card,ewallet,bank',
            'bank_ref' => 'nullable|string',
        ]);

        if (in_array($request->payment_type, ['card','bank']) && empty($request->bank_ref)) {
            return back()->withInput()->with('error', 'Vui lòng nhập mã tham chiếu cho thanh toán thẻ/ngân hàng.');
        }

        // Tạo đơn hàng
        $order = \App\Models\Order::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'total' => $total,
            'payment_type' => $request->payment_type === 'card' ? 'bank' : $request->payment_type,
            'note' => $request->note,
            'status' => 'pending',
        ]);

        // Tạo order items và lưu snapshot thông tin sản phẩm để đảm bảo chi tiết đơn hàng bất biến
        foreach ($cartItems as $item) {
            $product = $item->product;
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $product->price ?? 0,
                // snapshot fields
                'product_name' => $product->name ?? null,
                'product_image' => $product->image_url ?? null,
                'product_type' => $product->product_type ?? null,
                'product_reference' => $product->reference_id ?? null,
                'product_color_name' => optional($item->color)->name ?? null,
                'product_size' => $product->size ?? null,
            ]);
        }

        // Recompute persisted order total from order items (and model accessor)
        $order->load('orderItems');
        $order->total = $order->computed_total ?? $order->orderItems->sum(function($oi){ return ($oi->price ?? 0) * ($oi->quantity ?? 0); });
        $order->save();

        // Tạo payment record (mặc định pending để chờ admin xác nhận)
        DB::table('payments')->insert([
            'order_id' => $order->id,
            'method' => $request->payment_type === 'ewallet' ? 'e-wallet' : ($request->payment_type === 'cod' ? 'COD' : 'bank-card'),
            'amount' => $order->total,
            'status' => 'pending',
            'created_at' => now(),
        ]);

        // Xóa giỏ hàng
        $user->cart->items()->delete();

        // Chuyển sang màn hình chờ để admin xác nhận
        return redirect()->route('orders.waiting', $order->id);
    }

    public function remove($cartItemId)
    {
    /** @var \App\Models\User|null $user */
    $user = Auth::user();
        if (!$user || !$user->cart) {
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy giỏ hàng.');
        }
        $cartItem = \App\Models\CartItem::where('id', $cartItemId)->where('cart_id', $user->cart->id)->first();
        if ($cartItem) {
            $cartItem->delete();
            return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
        }
        return redirect()->route('cart.index')->with('error', 'Không tìm thấy sản phẩm trong giỏ hàng.');
    }

    public function index()
    {
    /** @var \App\Models\User|null $user */
    $user = Auth::user();
        $cartItems = collect();
        if ($user && $user->cart) {
            $cartItems = $user->cart->items()->with('product')->get();
        }
        return view('cart', compact('cartItems'));
    }

    public function add(Request $request)
    {
    /** @var \App\Models\User|null $user */
    $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.');
        }
        // Tìm hoặc tạo cart cho user
        $cart = $user->cart ?? \App\Models\Cart::firstOrCreate(['user_id' => $user->id]);
        $productId = $request->input('product_id');
        $quantity = max(1, (int)$request->input('quantity', 1));
        $cartItem = \App\Models\CartItem::where('cart_id', $cart->id)->where('product_id', $productId)->first();
        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            \App\Models\CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }
        // Nếu là mua ngay thì chuyển đến trang thanh toán
        if ($request->has('buy_now')) {
            return redirect('/cart/checkout');
        }
        return redirect()->route('cart.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    }
}
