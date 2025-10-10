<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            ProductsTableSeeder::class,
            PromotionsTableSeeder::class,
            CartsTableSeeder::class,
            CartItemsTableSeeder::class,
            OrdersTableSeeder::class,
            OrderDetailsTableSeeder::class,
            PaymentsTableSeeder::class,
            ReviewsTableSeeder::class,
        ]);
    }
}
