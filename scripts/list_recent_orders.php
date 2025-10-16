<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;

$orders = Order::with('user')->latest()->take(20)->get(['id','user_id','name','total','created_at']);
if ($orders->isEmpty()) {
    echo "No orders found.\n";
    exit(0);
}

foreach ($orders as $o) {
    $userName = $o->user ? $o->user->name : 'NULL';
    printf("ID: %s | user_id: %s | user.name: %s | order.name: %s | total: %s | created_at: %s\n", $o->id, $o->user_id ?? 'NULL', $userName, $o->name ?? 'NULL', $o->total ?? 'NULL', $o->created_at ?? 'NULL');
}
return 0;
