<?php
// One-off script to remove orders (and related payments/order_items) created by users with role = 'admin'
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;

// Find admin users (role = 'admin') and also detect orders where customer_name == 'Admin' or user.name == 'Admin'
$adminUsers = User::where('role', 'admin')->get();
$adminIds = $adminUsers->pluck('id')->toArray();

// Orders created by admin user_id OR orders where customer_name is 'Admin' OR linked user has name 'Admin'
$orderQuery = Order::query();
if (!empty($adminIds)) {
    $orderQuery->orWhereIn('user_id', $adminIds);
}
$orderQuery->orWhereRaw('LOWER(`name`) = ?', ['admin']);
$orderQuery->orWhereHas('user', function($q) { $q->whereRaw('LOWER(`name`) = ?', ['admin']); });
$orderIds = $orderQuery->pluck('id')->toArray();
if (empty($orderIds)) {
    echo "No orders found for admin users.\n";
    echo "Admin user IDs: " . json_encode($adminIds) . "\n";
    exit(0);
}

$paymentsDeleted = Payment::whereIn('order_id', $orderIds)->delete();
$orderItemsDeleted = OrderItem::whereIn('order_id', $orderIds)->delete();
$ordersDeleted = Order::whereIn('id', $orderIds)->delete();

echo "Admin user IDs: " . json_encode($adminIds) . "\n";
echo "Order IDs deleted: " . json_encode($orderIds) . "\n";
echo "Payments deleted: $paymentsDeleted\n";
echo "OrderItems deleted: $orderItemsDeleted\n";
echo "Orders deleted: $ordersDeleted\n";

return 0;
