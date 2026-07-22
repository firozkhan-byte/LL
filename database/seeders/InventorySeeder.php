<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouse = DB::table('warehouses')->where('code', 'WH-MUM-CTR')->first();
        $product1 = DB::table('products')->first();
        $product2 = DB::table('products')->skip(1)->first();

        if (! $warehouse || ! $product1) {
            return;
        }

        // 1. Opening stock for Product 1 (100 units @ ₹3000.00)
        DB::table('stock_ledgers')->insert([
            'id' => Str::uuid()->toString(),
            'product_id' => $product1->id,
            'warehouse_id' => $warehouse->id,
            'transaction_type' => 'opening',
            'quantity' => 100.00,
            'balance_after' => 100.00,
            'unit_price' => 3000.00,
            'batch_number' => 'BATCH-OP-01',
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        // 2. Purchase layer 1 for Product 1 (50 units @ ₹3500.00)
        DB::table('stock_ledgers')->insert([
            'id' => Str::uuid()->toString(),
            'product_id' => $product1->id,
            'warehouse_id' => $warehouse->id,
            'transaction_type' => 'purchase',
            'quantity' => 50.00,
            'balance_after' => 150.00,
            'unit_price' => 3500.00,
            'batch_number' => 'BATCH-PU-02',
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        // 3. Purchase layer 2 for Product 1 (30 units @ ₹3200.00)
        DB::table('stock_ledgers')->insert([
            'id' => Str::uuid()->toString(),
            'product_id' => $product1->id,
            'warehouse_id' => $warehouse->id,
            'transaction_type' => 'purchase',
            'quantity' => 30.00,
            'balance_after' => 180.00,
            'unit_price' => 3200.00,
            'batch_number' => 'BATCH-PU-03',
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        if ($product2) {
            // Opening stock for Product 2 (80 units @ ₹1000.00)
            DB::table('stock_ledgers')->insert([
                'id' => Str::uuid()->toString(),
                'product_id' => $product2->id,
                'warehouse_id' => $warehouse->id,
                'transaction_type' => 'opening',
                'quantity' => 80.00,
                'balance_after' => 80.00,
                'unit_price' => 1000.00,
                'batch_number' => 'BATCH-OP-GF',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ]);

            // Adjust stock write-off due to damage (decrement 5 units)
            $adjId = Str::uuid()->toString();
            $user = DB::table('users')->first();
            DB::table('stock_adjustments')->insert([
                'id' => $adjId,
                'code' => 'ADJ-2026-0001',
                'warehouse_id' => $warehouse->id,
                'reason' => 'damaged',
                'status' => 'completed',
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('stock_adjustment_items')->insert([
                'id' => Str::uuid()->toString(),
                'stock_adjustment_id' => $adjId,
                'product_id' => $product2->id,
                'adjustment_type' => 'decrement',
                'quantity' => 5.00,
                'batch_number' => 'BATCH-OP-GF',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('stock_ledgers')->insert([
                'id' => Str::uuid()->toString(),
                'product_id' => $product2->id,
                'warehouse_id' => $warehouse->id,
                'transaction_type' => 'adjustment_remove',
                'quantity' => -5.00,
                'balance_after' => 75.00,
                'unit_price' => 1000.00,
                'reference_type' => 'StockAdjustment',
                'reference_id' => $adjId,
                'batch_number' => 'BATCH-OP-GF',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
