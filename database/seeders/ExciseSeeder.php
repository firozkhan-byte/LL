<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supplier = DB::table('suppliers')->first();
        $product = DB::table('products')->first();

        // 1. Seed HSN Code
        $hsnId = Str::uuid()->toString();
        DB::table('hsn_codes')->insert([
            'id' => $hsnId,
            'code' => '2208',
            'description' => 'Spirits & Liqueurs (Whisky, Rum, Gin, Vodka)',
            'gst_rate' => 18.00,
            'excise_duty_rate' => 150.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Map product to HSN
        if ($product) {
            DB::table('products')
                ->where('id', $product->id)
                ->update(['hsn_code_id' => $hsnId]);
        }

        // 2. Seed active Excise License
        $licId = Str::uuid()->toString();
        DB::table('excise_licenses')->insert([
            'id' => $licId,
            'license_number' => 'LIC-EX-MH-9999',
            'license_type' => 'FL-III retail shop',
            'state' => 'Maharashtra',
            'expiry_date' => now()->addMonths(6)->format('Y-m-d'),
            'status' => 'active',
            'renewal_fee' => 150000.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Seed Excise Permit
        if ($supplier) {
            DB::table('excise_permits')->insert([
                'id' => Str::uuid()->toString(),
                'permit_number' => 'PRM-MH-1002',
                'excise_license_id' => $licId,
                'supplier_id' => $supplier->id,
                'issue_date' => now()->subDays(10)->format('Y-m-d'),
                'expiry_date' => now()->addDays(5)->format('Y-m-d'),
                'status' => 'utilized',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Seed Daily Excise Register
        if ($product) {
            DB::table('excise_registers')->insert([
                'id' => Str::uuid()->toString(),
                'transaction_date' => now()->format('Y-m-d'),
                'excise_license_id' => $licId,
                'product_id' => $product->id,
                'opening_balance' => 100.00,
                'received_quantity' => 50.00,
                'sold_quantity' => 3.00,
                'closing_balance' => 147.00,
                'excise_duty_paid' => 7500.00, // 50 units received * 150 duty rate
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
