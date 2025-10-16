<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;

class InspectOrder extends Command
{
    protected $signature = 'orders:inspect {orderId}';
    protected $description = 'Print diagnostic info for an order and its items';

    public function handle()
    {
        $orderId = $this->argument('orderId');
        $order = Order::with(['orderItems', 'orderItems.product', 'payment', 'user'])->find($orderId);
        if (!$order) {
            $this->error("Order {$orderId} not found.");
            return 1;
        }

        $this->info("Order #{$order->id} summary:");
        $this->line(" - customer_name: " . ($order->customer_name ?? $order->name ?? 'N/A'));
    $this->line(" - total (db): " . ($order->total ?? 'NULL'));
    $this->line(" - total (computed): " . number_format($order->computed_total, 0, ',', '.'));
        $this->line(" - status: " . ($order->status ?? 'NULL'));
        $this->line(" - orderItems count: " . $order->orderItems->count());

        if ($order->orderItems->isEmpty()) {
            $this->warn('No order items found for this order.');
            return 0;
        }

        $rows = [];
        foreach ($order->orderItems as $item) {
            $productExists = $item->product ? 'yes' : 'no';
            $rows[] = [
                $item->id,
                $item->product_id,
                $productExists,
                $item->product_name ?? 'NULL',
                $item->price,
                $item->quantity,
            ];
        }

        $this->table(['item_id','product_id','product_exists','product_name_snapshot','price','qty'], $rows);
        return 0;
    }
}
