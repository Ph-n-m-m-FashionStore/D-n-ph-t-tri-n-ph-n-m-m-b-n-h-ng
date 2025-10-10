<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewsTableSeeder extends Seeder {
    public function run() {
        $userIds = DB::table('users')->pluck('id')->toArray();
        $productIds = DB::table('products')->pluck('id')->toArray();
        $reviews = [];
        if (count($userIds) > 0 && count($productIds) > 0) {
            $reviews[] = [
                'user_id' => $userIds[0],
                'product_id' => $productIds[0],
                'rating' => 5,
                'comment' => 'Áo đẹp, chất vải tốt, giao hàng nhanh!'
            ];
        }
        if (count($userIds) > 1 && count($productIds) > 1) {
            $reviews[] = [
                'user_id' => $userIds[1],
                'product_id' => $productIds[1],
                'rating' => 4,
                'comment' => 'Màu sắc như hình, sẽ ủng hộ tiếp.'
            ];
        }
        DB::table('reviews')->delete();
        if (count($reviews) > 0) {
            DB::table('reviews')->insert($reviews);
        }
    }
}
