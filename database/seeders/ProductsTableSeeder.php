<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder {
    public function run() {
        // Xóa dữ liệu cũ trước khi seed
        DB::table('products')->delete();

        $products = [
            [
                'name' => 'Áo thun nam basic',
                'description' => 'Áo thun nam chất liệu cotton, thoáng mát, phù hợp mọi hoạt động.',
                'brand' => 'FashionBrand',
                'size' => 'M',
                'gender' => 'nam',
                'price' => 150000,
                'stock' => 100,
                'image_url' => '/images/ao-thun-nam.png',
            ],
            [
                'name' => 'Quần jeans nữ',
                'description' => 'Quần jeans nữ co giãn, form đẹp, dễ phối đồ.',
                'brand' => 'JeansPro',
                'size' => 'L',
                'gender' => 'nu',
                'price' => 320000,
                'stock' => 50,
                'image_url' => '/images/quan-jeans-nu.png',
            ],
            [
                'name' => 'Váy xòe hoa',
                'description' => 'Váy xòe họa tiết hoa, chất liệu mềm mại, nữ tính.',
                'brand' => 'FlowerDress',
                'size' => 'S',
                'gender' => 'nu',
                'price' => 250000,
                'stock' => 30,
                'image_url' => '/images/vay-xoe-hoa.png',
            ],
        ];

        

        DB::table('products')->insert($products);
    }
}
