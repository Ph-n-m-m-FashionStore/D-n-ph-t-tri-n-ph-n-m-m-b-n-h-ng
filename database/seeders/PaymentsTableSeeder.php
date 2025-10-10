<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentsTableSeeder extends Seeder {
    public function run() {
        $orderIds = DB::table('orders')->pluck('id')->toArray();
        $payments = [];
        if (count($orderIds) > 0) {
            $payments[] = [
                'order_id' => $orderIds[0],
                'method' => 'COD',
                'amount' => 300000,
                'status' => 'pending',
            ];
        }
        if (count($orderIds) > 1) {
            $payments[] = [
                'order_id' => $orderIds[1],
                'method' => 'bank-card',
                'amount' => 250000,
                'status' => 'paid',
            ];
        }
        DB::table('payments')->delete();
        if (count($payments) > 0) {
            DB::table('payments')->insert($payments);
        }
    }
}
