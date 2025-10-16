<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$customers = User::where('role','customer')->with('orders.orderItems')->take(5)->get();
foreach ($customers as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | computed_total_spent: {$u->computed_total_spent} | total_spent (sum_col): ";
    echo ($u->total_spent ?? 'NULL') . PHP_EOL;
}
