<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class POSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Customers
        DB::table('customers')->insert([
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Rahul Sharma',
                'phone' => '9876543210',
                'email' => 'rahul@gmail.com',
                'membership_type' => 'regular',
                'loyalty_points' => 120,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Anita Patel',
                'phone' => '9123456789',
                'email' => 'anita@gmail.com',
                'membership_type' => 'gold',
                'loyalty_points' => 450,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 2. Coupons
        DB::table('coupons')->insert([
            [
                'id' => Str::uuid()->toString(),
                'code' => 'LUCKY100',
                'discount_type' => 'fixed',
                'discount_value' => 100.00,
                'min_purchase_amount' => 500.00,
                'expires_at' => now()->addMonth(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'code' => 'PROMO20',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'min_purchase_amount' => 1000.00,
                'expires_at' => now()->addMonth(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 3. Gift Cards
        DB::table('gift_cards')->insert([
            [
                'id' => Str::uuid()->toString(),
                'card_number' => 'GC-CARD-999',
                'balance' => 5000.00,
                'expires_at' => now()->addYear(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
