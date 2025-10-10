<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartItemsTableSeeder extends Seeder {
    public function run() {
        // Lấy danh sách cart_id và product_id hiện có
        $cartIds = DB::table('carts')->pluck('id')->toArray();
        $productIds = DB::table('products')->pluck('id')->toArray();
        $items = [];
        if (count($cartIds) > 0 && count($productIds) > 1) {
            // Gán sản phẩm cho giỏ đầu tiên
            $items[] = ['cart_id' => $cartIds[0], 'product_id' => $productIds[0], 'quantity' => 2];
            $items[] = ['cart_id' => $cartIds[0], 'product_id' => $productIds[1], 'quantity' => 1];
        }
        if (count($cartIds) > 1 && count($productIds) > 2) {
            // Gán sản phẩm cho giỏ thứ hai
            $items[] = ['cart_id' => $cartIds[1], 'product_id' => $productIds[2], 'quantity' => 1];
        }
        DB::table('cart_items')->delete();
        if (count($items) > 0) {
            DB::table('cart_items')->insert($items);
        }
    }
}
