<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersTableSeeder extends Seeder {
    public function run() {
        $userIds = DB::table('users')->pluck('id')->toArray();
        $orders = [];
        if (count($userIds) > 0) {
            $orders[] = [
                'user_id' => $userIds[0],
                'status' => 'shipping',
                'total' => 300000,
            ];
        }
        if (count($userIds) > 1) {
            $orders[] = [
                'user_id' => $userIds[1],
                'status' => 'completed',
                'total' => 250000,
            ];
        }
        DB::table('orders')->delete();
        if (count($orders) > 0) {
            DB::table('orders')->insert($orders);
        }
    }
}
