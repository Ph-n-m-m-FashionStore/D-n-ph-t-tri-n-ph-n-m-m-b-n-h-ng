<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartsTableSeeder extends Seeder {
    public function run() {
        // Lấy danh sách user_id hiện có
        $userIds = DB::table('users')->pluck('id')->toArray();
        $carts = [];
        foreach ($userIds as $id) {
            $carts[] = ['user_id' => $id];
        }
        DB::table('carts')->delete();
        DB::table('carts')->insert($carts);
    }
}
