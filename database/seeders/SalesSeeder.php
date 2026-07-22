<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouse = DB::table('warehouses')->where('code', 'WH-MUM-CTR')->first();
        $product1 = DB::table('products')->first();
        $customer1 = DB::table('customers')->first();
        $customer2 = DB::table('customers')->skip(1)->first();

        if (! $warehouse || ! $product1 || ! $customer1) {
            return;
        }

        // 1. Corporate Order
        $so1Id = Str::uuid()->toString();
        DB::table('sales_orders')->insert([
            'id' => $so1Id,
            'order_number' => 'SO-2026-0001',
            'customer_id' => $customer1->id,
            'warehouse_id' => $warehouse->id,
            'order_type' => 'corporate',
            'status' => 'delivered',
            'payment_status' => 'paid',
            'delivery_address' => 'Corporate Hub, Lower Parel, Mumbai',
            'subtotal' => 6000.00,
            'tax_amount' => 1080.00,
            'total_amount' => 7080.00,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        DB::table('sales_order_items')->insert([
            'id' => Str::uuid()->toString(),
            'sales_order_id' => $so1Id,
            'product_id' => $product1->id,
            'quantity' => 2.00,
            'unit_price' => 3000.00,
            'total_price' => 6000.00,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        $invId = Str::uuid()->toString();
        DB::table('sales_invoices')->insert([
            'id' => $invId,
            'invoice_number' => 'INV-SL-2026-0001',
            'sales_order_id' => $so1Id,
            'total_amount' => 7080.00,
            'status' => 'completed',
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        // Deduct stock for corporate order
        DB::table('stock_ledgers')->insert([
            'id' => Str::uuid()->toString(),
            'product_id' => $product1->id,
            'warehouse_id' => $warehouse->id,
            'transaction_type' => 'sale',
            'quantity' => -2.00,
            'balance_after' => 178.00, // assuming inventory seeder running balance
            'unit_price' => 3000.00,
            'reference_type' => 'SalesOrder',
            'reference_id' => $so1Id,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        // 2. Online Order
        if ($customer2) {
            $so2Id = Str::uuid()->toString();
            DB::table('sales_orders')->insert([
                'id' => $so2Id,
                'order_number' => 'SO-2026-0002',
                'customer_id' => $customer2->id,
                'warehouse_id' => $warehouse->id,
                'order_type' => 'online',
                'status' => 'processing',
                'payment_status' => 'unpaid',
                'delivery_address' => 'Bandra West, Mumbai',
                'subtotal' => 3000.00,
                'tax_amount' => 540.00,
                'total_amount' => 3540.00,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ]);

            DB::table('sales_order_items')->insert([
                'id' => Str::uuid()->toString(),
                'sales_order_id' => $so2Id,
                'product_id' => $product1->id,
                'quantity' => 1.00,
                'unit_price' => 3000.00,
                'total_price' => 3000.00,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ]);
        }

        // 3. Completed Sales Return (1 unit of product 1 returned)
        $retId = Str::uuid()->toString();
        DB::table('sales_returns')->insert([
            'id' => $retId,
            'return_number' => 'RET-2026-0001',
            'sales_order_id' => $so1Id,
            'reason' => 'wrong_item',
            'refund_amount' => 3540.00,
            'status' => 'completed',
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        DB::table('sales_return_items')->insert([
            'id' => Str::uuid()->toString(),
            'sales_return_id' => $retId,
            'product_id' => $product1->id,
            'quantity' => 1.00,
            'refund_unit_price' => 3000.00,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        // Issue Credit Note
        DB::table('credit_notes')->insert([
            'id' => Str::uuid()->toString(),
            'note_number' => 'CN-2026-0001',
            'customer_id' => $customer1->id,
            'sales_return_id' => $retId,
            'amount' => 3540.00,
            'status' => 'active',
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        // Put returned stock back into ledger
        DB::table('stock_ledgers')->insert([
            'id' => Str::uuid()->toString(),
            'product_id' => $product1->id,
            'warehouse_id' => $warehouse->id,
            'transaction_type' => 'return',
            'quantity' => 1.00,
            'balance_after' => 179.00,
            'unit_price' => 3000.00,
            'reference_type' => 'SalesReturn',
            'reference_id' => $retId,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);
    }
}
