<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use Illuminate\Support\Facades\DB;

$mode = $argv[1] ?? 'dry-run'; // 'dry-run' or 'apply'
$limit = isset($argv[2]) ? (int)$argv[2] : null; // optional limit for testing

$query = Order::where('status', 'completed')->orderBy('id');
if ($limit) $query->limit($limit);

$orders = $query->with('orderItems')->get();

$total = $orders->count();
$diffs = [];
foreach ($orders as $order) {
    $computed = $order->computed_total ?? ($order->total ?? 0);
    $stored = $order->total ?? 0;
    // Normalize rounding to integer cents
    $c1 = (int) round($computed);
    $c2 = (int) round($stored);
    if ($c1 !== $c2) {
        $diffs[] = [
            'id' => $order->id,
            'stored' => $stored,
            'computed' => $computed,
        ];
    }
}

echo "Scanned orders: {$total}\n";
echo "Mismatched orders: " . count($diffs) . "\n";
if (count($diffs) > 0) {
    echo "Sample diffs (up to 20):\n";
    $sample = array_slice($diffs, 0, 20);
    foreach ($sample as $d) {
        echo "Order #{$d['id']} | stored: {$d['stored']} | computed: {$d['computed']}\n";
    }
}

if ($mode === 'apply' && count($diffs) > 0) {
    echo "Applying updates...\n";
    DB::beginTransaction();
    try {
        foreach ($diffs as $d) {
            Order::where('id', $d['id'])->update(['total' => $d['computed']]);
            echo "Updated order {$d['id']}\n";
        }
        DB::commit();
        echo "Apply complete. Updated " . count($diffs) . " orders.\n";
    } catch (\Exception $e) {
        DB::rollBack();
        echo "Error during apply: " . $e->getMessage() . "\n";
    }
} else {
    echo "Run with 'apply' to update stored totals.\n";
}
