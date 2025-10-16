<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem;
use App\Models\Product;

class BackfillOrderItemSnapshots extends Command
{
    protected $signature = 'orders:backfill-snapshots {--batch=100}';
    protected $description = 'Backfill snapshot fields on existing order_items from current product data';

    public function handle()
    {
        $batch = (int)$this->option('batch');
        $this->info("Backfilling order_items snapshots in batches of {$batch}...");

        $query = OrderItem::whereNull('product_name')->orWhereNull('product_image');
        $total = $query->count();
        $this->info("Found {$total} order items to update.");

        $processed = 0;
        $query->chunkById($batch, function($items) use (&$processed) {
            foreach ($items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $item->update([
                        'product_name' => $product->name,
                        'product_image' => $product->image_url,
                        'product_type' => $product->product_type,
                        'product_reference' => $product->reference_id,
                        'product_size' => $product->size,
                    ]);
                }
                $processed++;
                if ($processed % 50 == 0) $this->info("Processed: {$processed}");
            }
        });

        $this->info("Done. Processed {$processed} items.");
        return 0;
    }
}
