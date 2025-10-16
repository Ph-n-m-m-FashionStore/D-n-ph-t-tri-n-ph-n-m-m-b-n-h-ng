<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportController;

// ==================== PUBLIC ROUTES ====================

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Sản phẩm (public)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// Theo dõi đơn hàng (public - không cần login)
Route::get('/order-status', [OrderStatusController::class, 'index'])->name('order-status.index');

// Khuyến mãi (public)
Route::resource('promotions', PromotionController::class)->only(['index', 'show']);

// Đánh giá sản phẩm (cần auth)
Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store')
    ->middleware('auth');

// ==================== AUTHENTICATION ROUTES ====================
// Quên mật khẩu bằng OTP
Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'showEmailForm'])->name('password.otp.email');
Route::post('/forgot-password/send-otp', [App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'sendOtp'])->name('password.otp.send');
Route::get('/forgot-password/verify-otp', [App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'showOtpForm'])->name('password.otp.form');
Route::post('/forgot-password/verify-otp', [App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'verifyOtp'])->name('password.otp.verify');
Route::get('/forgot-password/reset', [App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'showResetForm'])->name('password.otp.reset');
Route::post('/forgot-password/reset', [App\Http\Controllers\Auth\ForgotPasswordOtpController::class, 'resetPassword'])->name('password.otp.update');

// Đăng nhập
Route::get('/login', function() {
    return view('auth.login');
})->name('login');

Route::post('/login', function() {
    $credentials = request()->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);
    if (Auth::attempt($credentials)) {
        request()->session()->regenerate();
        // Check email verified
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        // Allow admin accounts to login without email verification
        if ($authUser && !$authUser->hasVerifiedEmail() && ($authUser->role ?? null) !== 'admin') {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Bạn cần xác thực email trước khi đăng nhập. Vui lòng kiểm tra hộp thư.',
            ])->withInput();
        }
        // Redirect admin đến dashboard, user thường đến home
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('home');
    }
    return back()->withErrors([
        'email' => 'Email hoặc mật khẩu không đúng.',
    ])->withInput();
});

// Đăng ký
Route::get('/register', function() {
    return view('auth.register');
})->name('register');

Route::post('/register', function() {
    $data = request()->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => ['required', 'string', 'confirmed', new \App\Rules\StrongPassword()],
        'phone' => 'nullable|string|max:20|unique:users,phone',
        'address' => 'nullable|string|max:500'
    ], [
        'phone.unique' => 'Số điện thoại này đã được sử dụng. Vui lòng dùng số khác hoặc liên hệ hỗ trợ.'
    ]);
    
    $user = \App\Models\User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => bcrypt($data['password']),
        'phone' => $data['phone'] ?? null,
        'address' => $data['address'] ?? null,
        'role' => 'customer' // Mặc định là customer
    ]);
    // Send email verification notification
    $user->sendEmailVerificationNotification();

    // Show confirmation page telling user to check email
    return view('auth.verify_sent', ['email' => $user->email]);
});

// Đăng xuất
Route::post('/logout', function() {
    Auth::logout();
    return redirect()->route('home');
})->name('logout');

// ==================== AUTHENTICATED USER ROUTES ====================

Route::middleware(['auth'])->group(function () {
    
    // Giỏ hàng
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::patch('/update/{cartItem}', [CartController::class, 'update'])->name('update');
        Route::delete('/remove/{cartItem}', [CartController::class, 'remove'])->name('remove');
        Route::post('/clear', [CartController::class, 'clear'])->name('clear');
        Route::get('/checkout', [CartController::class, 'showCheckoutForm'])->name('checkout.index');
        Route::post('/checkout', [CartController::class, 'processCheckout'])->name('checkout');
    });

    // Đơn hàng (user)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders-statuses', [OrderController::class, 'statuses'])->name('orders.statuses');
    Route::get('/orders/{order}/waiting', [OrderController::class, 'waiting'])->name('orders.waiting');
    Route::get('/orders/{order}/status', [OrderController::class, 'status'])->name('orders.status');

    // Hồ sơ cá nhân
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.view');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Thông tin tài khoản (view & edit)
    Route::get('/account', [App\Http\Controllers\AccountController::class, 'show'])->name('account.show');
    Route::get('/account/edit', [App\Http\Controllers\AccountController::class, 'edit'])->name('account.edit');
    Route::put('/account', [App\Http\Controllers\AccountController::class, 'update'])->name('account.update');
    Route::put('/account/password', [App\Http\Controllers\AccountController::class, 'updatePassword'])->name('account.password');

    // Theo dõi đơn hàng (authenticated)
    Route::post('/order-status/update/{id}', [OrderStatusController::class, 'update'])
        ->name('order-status.update');
});

// Email verification callback (signed URL)
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\URL;

Route::get('/email/verify/{id}/{hash}', function (HttpRequest $request, $id, $hash) {
    // This route emulates Laravel's email verification endpoint.
    $user = \App\Models\User::find($id);
    if (!$user) {
        return redirect()->route('login')->with('error', 'Người dùng không tồn tại.');
    }
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return redirect()->route('login')->with('error', 'Liên kết xác thực không hợp lệ.');
    }
    $user->markEmailAsVerified();
    return redirect()->route('login')->with('success', 'Email đã được xác thực. Bạn có thể đăng nhập bây giờ.');
})->name('verification.verify');


// ==================== ADMIN ROUTES ====================

Route::middleware(['auth', 'admin'])->group(function () {
    
    // Admin Dashboard
    Route::get('/admin/dashboard', [ReportController::class, 'dashboard'])->name('admin.dashboard');

    // Admin Products Management
    Route::get('/admin/products', [ProductController::class, 'adminIndex'])->name('admin.products.index');
    Route::get('/admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/admin/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::post('/admin/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('admin.products.toggle-status');

    // Admin Orders Management - ĐÃ SỬA LỖI LẶP ROUTES
    Route::get('/admin/orders', [OrderController::class, 'adminIndex'])->name('admin.orders.index');
    Route::get('/admin/orders/{order}', [OrderController::class, 'adminShow'])->name('admin.orders.show');
    Route::post('/admin/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.update-status');
    Route::post('/admin/orders/{order}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('admin.orders.confirm-payment');
    Route::put('/admin/orders/{order}/info', [OrderController::class, 'updateOrderInfo'])->name('admin.orders.update-info');
    Route::delete('/admin/orders/{order}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');

    // Admin Customers Management
    Route::get('/admin/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/admin/customers/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');
    Route::put('/admin/customers/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::post('/admin/customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('admin.customers.toggle-status');
    Route::delete('/admin/customers/{customer}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');
    Route::get('/admin/customers/{customer}/orders', [CustomerController::class, 'orderHistory'])->name('admin.customers.order-history');

    // Admin Reports
    Route::get('/admin/reports/sales', [ReportController::class, 'sales'])->name('admin.reports.sales');
    Route::get('/admin/reports/products', [ReportController::class, 'products'])->name('admin.reports.products');
    Route::get('/admin/reports/customers', [ReportController::class, 'customers'])->name('admin.reports.customers');

    // Admin Reviews Management
    Route::resource('admin/reviews', \App\Http\Controllers\Admin\ReviewController::class)
        ->only(['index', 'show', 'destroy'])
        ->names([
            'index' => 'admin.reviews.index',
            'show' => 'admin.reviews.show',
            'destroy' => 'admin.reviews.destroy',
        ]);
});

// ==================== FALLBACK ROUTE ====================

Route::fallback(function () {
    return view('errors.404');
});