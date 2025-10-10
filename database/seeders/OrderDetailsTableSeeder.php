<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderDetailsTableSeeder extends Seeder {
    public function run() {
        $orderIds = DB::table('orders')->pluck('id')->toArray();
        $productIds = DB::table('products')->pluck('id')->toArray();
        $details = [];
        if (count($orderIds) > 0 && count($productIds) > 1) {
            $details[] = ['order_id' => $orderIds[0], 'product_id' => $productIds[0], 'quantity' => 2, 'price' => 150000];
            $details[] = ['order_id' => $orderIds[0], 'product_id' => $productIds[1], 'quantity' => 1, 'price' => 320000];
        }
        if (count($orderIds) > 1 && count($productIds) > 2) {
            $details[] = ['order_id' => $orderIds[1], 'product_id' => $productIds[2], 'quantity' => 1, 'price' => 250000];
        }
        DB::table('order_details')->delete();
        if (count($details) > 0) {
            DB::table('order_details')->insert($details);
        }
    }
}
