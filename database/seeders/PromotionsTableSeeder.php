<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromotionsTableSeeder extends Seeder {
    public function run() {
        // Xóa toàn bộ dữ liệu cũ để tránh lỗi trùng code, không dùng truncate vì có thể có ràng buộc khóa ngoại
        DB::table('promotions')->delete();
        DB::table('promotions')->insert([
            [
                'code' => 'SALE50',
                'discount_percent' => 50,
                'start_date' => '2025-09-01',
                'end_date' => '2025-09-30',
            ],
            [
                'code' => 'FREESHIP',
                'discount_percent' => 0,
                'start_date' => '2025-09-01',
                'end_date' => '2025-09-30',
            ],
        ]);
    }
}
