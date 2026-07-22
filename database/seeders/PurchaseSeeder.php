<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch existing users, products, suppliers
        $user = DB::table('users')->first();
        $product1 = DB::table('products')->first();
        $product2 = DB::table('products')->skip(1)->first();
        $supplier = DB::table('suppliers')->first();

        if (! $user || ! $product1 || ! $supplier) {
            return;
        }

        // 1. Seed Purchase Requisition
        $reqId = Str::uuid()->toString();
        DB::table('purchase_requisitions')->insert([
            'id' => $reqId,
            'code' => 'REQ-2026-0001',
            'requested_by' => $user->id,
            'needed_by_date' => now()->addDays(7)->toDateString(),
            'status' => 'approved',
            'remarks' => 'Restocking whiskey for Speyside showcase next week.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('purchase_requisition_items')->insert([
            [
                'id' => Str::uuid()->toString(),
                'purchase_requisition_id' => $reqId,
                'product_id' => $product1->id,
                'quantity' => 100,
                'estimated_cost' => 3500.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'purchase_requisition_id' => $reqId,
                'product_id' => $product2->id,
                'quantity' => 50,
                'estimated_cost' => 6500.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 2. Seed Purchase Order
        $poId = Str::uuid()->toString();
        DB::table('purchase_orders')->insert([
            'id' => $poId,
            'code' => 'PO-2026-0001',
            'supplier_id' => $supplier->id,
            'purchase_requisition_id' => $reqId,
            'po_date' => now()->toDateString(),
            'payment_terms' => '45 Days Credit',
            'status' => 'approved',
            'subtotal' => 675000.00,
            'tax_amount' => 121500.00,
            'total_amount' => 796500.00,
            'approved_by' => $user->id,
            'remarks' => 'Direct restocking order linked to approved REQ-2026-0001.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('purchase_order_items')->insert([
            [
                'id' => Str::uuid()->toString(),
                'purchase_order_id' => $poId,
                'product_id' => $product1->id,
                'quantity' => 100,
                'unit_price' => 3500.00,
                'tax_percent' => 18.00,
                'tax_amount' => 63000.00,
                'total_amount' => 413000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'purchase_order_id' => $poId,
                'product_id' => $product2->id,
                'quantity' => 50,
                'unit_price' => 6500.00,
                'tax_percent' => 18.00,
                'tax_amount' => 58500.00,
                'total_amount' => 383500.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 3. Seed Goods Receipt Note (GRN)
        $grnId = Str::uuid()->toString();
        DB::table('goods_receipt_notes')->insert([
            'id' => $grnId,
            'code' => 'GRN-2026-0001',
            'purchase_order_id' => $poId,
            'received_date' => now()->toDateString(),
            'received_by' => $user->id,
            'status' => 'completed',
            'remarks' => 'Delivered in full. Outer boxes intact.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('goods_receipt_note_items')->insert([
            [
                'id' => Str::uuid()->toString(),
                'goods_receipt_note_id' => $grnId,
                'product_id' => $product1->id,
                'quantity_ordered' => 100,
                'quantity_received' => 100,
                'quantity_accepted' => 100,
                'quantity_rejected' => 0,
                'batch_number' => 'BATCH-JW-100X',
                'expiry_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'goods_receipt_note_id' => $grnId,
                'product_id' => $product2->id,
                'quantity_ordered' => 50,
                'quantity_received' => 50,
                'quantity_accepted' => 50,
                'quantity_rejected' => 0,
                'batch_number' => 'BATCH-GF-50Y',
                'expiry_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 4. Seed Purchase Invoice
        $piId = Str::uuid()->toString();
        DB::table('purchase_invoices')->insert([
            'id' => $piId,
            'code' => 'INV-2026-0001',
            'supplier_id' => $supplier->id,
            'goods_receipt_note_id' => $grnId,
            'invoice_number' => 'USL/INV/98765',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(45)->toDateString(),
            'status' => 'unpaid',
            'subtotal' => 675000.00,
            'tax_amount' => 121500.00,
            'total_amount' => 796500.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
