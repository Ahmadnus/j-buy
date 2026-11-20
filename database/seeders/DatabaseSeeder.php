<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Delivery areas — registration and checkout depend on them.
            ShippingRateSeeder::class,
            CategorySeeder::class,
            BannerSeeder::class,
            ProductSeeder::class,
            // Skips itself unless ADMIN_EMAIL and ADMIN_PASSWORD are set.
            AdminUserSeeder::class,
        ]);
    }
}
