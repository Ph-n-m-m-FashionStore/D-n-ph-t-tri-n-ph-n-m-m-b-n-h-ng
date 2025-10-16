<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem;
use App\Models\Product;

class ImportOrderDetails extends Command
{
    protected $signature = 'orders:import-details {--dry-run}';
    protected $description = 'Import rows from order_details table into order_items (copies snapshots)';

    public function handle()
    {
        $dry = $this->option('dry-run');
        $this->info('Scanning order_details...');

        $details = DB::table('order_details')->get();
        if ($details->isEmpty()) {
            $this->info('No rows found in order_details.');
            return 0;
        }

        $created = 0;
        foreach ($details as $d) {
            // skip if an equivalent order_items record exists
            $exists = OrderItem::where('order_id', $d->order_id)
                ->where('product_id', $d->product_id)
                ->where('price', $d->price)
                ->where('quantity', $d->quantity)
                ->exists();
            if ($exists) continue;

            $product = Product::find($d->product_id);
            $data = [
                'order_id' => $d->order_id,
                'product_id' => $d->product_id,
                'quantity' => $d->quantity,
                'price' => $d->price,
                'product_name' => $product->name ?? null,
                'product_image' => $product->image_url ?? null,
                'product_type' => $product->product_type ?? null,
                'product_reference' => $product->reference_id ?? null,
                'product_color_name' => null,
                'product_size' => $product->size ?? null,
            ];

            if ($dry) {
                $this->line('Would create order_item for order ' . $d->order_id . ' product ' . $d->product_id);
                continue;
            }

            OrderItem::create($data);
            $created++;
        }

        $this->info("Import complete. Created {$created} order_items.");
        return 0;
    }
}
