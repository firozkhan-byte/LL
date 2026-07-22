<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Purchase Requisitions
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->uuid('requested_by');
            $table->date('needed_by_date');
            $table->string('status')->default('draft'); // draft, pending_approval, approved, rejected, ordered
            $table->text('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('requested_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        // 2. Purchase Requisition Items
        Schema::create('purchase_requisition_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('purchase_requisition_id');
            $table->uuid('product_id');
            $table->decimal('quantity', 10, 2);
            $table->decimal('estimated_cost', 15, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('purchase_requisition_id')
                ->references('id')
                ->on('purchase_requisitions')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });

        // 3. Purchase Orders
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->uuid('supplier_id');
            $table->uuid('purchase_requisition_id')->nullable();
            $table->date('po_date');
            $table->string('payment_terms')->nullable();
            $table->string('status')->default('draft'); // draft, pending_approval, approved, rejected, sent, partially_received, received, cancelled
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->uuid('approved_by')->nullable();
            $table->text('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->cascadeOnDelete();

            $table->foreign('purchase_requisition_id')
                ->references('id')
                ->on('purchase_requisitions')
                ->nullOnDelete();

            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });

        // 4. Purchase Order Items
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('purchase_order_id');
            $table->uuid('product_id');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('tax_percent', 5, 2)->default(18.00);
            $table->decimal('tax_amount', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();

            $table->foreign('purchase_order_id')
                ->references('id')
                ->on('purchase_orders')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });

        // 5. Goods Receipt Notes (GRN)
        Schema::create('goods_receipt_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->uuid('purchase_order_id');
            $table->date('received_date');
            $table->uuid('received_by');
            $table->string('status')->default('completed'); // draft, completed, returned
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('purchase_order_id')
                ->references('id')
                ->on('purchase_orders')
                ->cascadeOnDelete();

            $table->foreign('received_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        // 6. Goods Receipt Note Items
        Schema::create('goods_receipt_note_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('goods_receipt_note_id');
            $table->uuid('product_id');
            $table->decimal('quantity_ordered', 10, 2);
            $table->decimal('quantity_received', 10, 2);
            $table->decimal('quantity_accepted', 10, 2);
            $table->decimal('quantity_rejected', 10, 2)->default(0.00);
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();

            $table->foreign('goods_receipt_note_id')
                ->references('id')
                ->on('goods_receipt_notes')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });

        // 7. Purchase Invoices
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->uuid('supplier_id');
            $table->uuid('goods_receipt_note_id')->nullable();
            $table->string('invoice_number'); // vendor reference
            $table->date('invoice_date');
            $table->date('due_date');
            $table->string('status')->default('unpaid'); // unpaid, paid, partially_paid
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->cascadeOnDelete();

            $table->foreign('goods_receipt_note_id')
                ->references('id')
                ->on('goods_receipt_notes')
                ->nullOnDelete();
        });

        // 8. Purchase Invoice Items
        Schema::create('purchase_invoice_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('purchase_invoice_id');
            $table->uuid('product_id');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('tax_amount', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();

            $table->foreign('purchase_invoice_id')
                ->references('id')
                ->on('purchase_invoices')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoice_items');
        Schema::dropIfExists('purchase_invoices');
        Schema::dropIfExists('goods_receipt_note_items');
        Schema::dropIfExists('goods_receipt_notes');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('purchase_requisition_items');
        Schema::dropIfExists('purchase_requisitions');
    }
};
