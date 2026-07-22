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
        // 1. Sales Orders
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number')->unique();
            $table->uuid('customer_id')->nullable();
            $table->uuid('warehouse_id');
            $table->string('order_type')->default('walk_in'); // online, corporate, wholesale, walk_in
            $table->string('status')->default('pending'); // pending, processing, shipped, delivered, cancelled
            $table->string('payment_status')->default('unpaid'); // paid, unpaid, partially_paid
            $table->string('delivery_address')->nullable();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->nullOnDelete();

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->cascadeOnDelete();
        });

        // 2. Sales Order Items
        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sales_order_id');
            $table->uuid('product_id');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->timestamps();

            $table->foreign('sales_order_id')
                ->references('id')
                ->on('sales_orders')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });

        // 3. Sales Invoices
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('invoice_number')->unique();
            $table->uuid('sales_order_id');
            $table->decimal('total_amount', 15, 2);
            $table->string('status')->default('draft'); // draft, completed, cancelled
            $table->timestamps();

            $table->foreign('sales_order_id')
                ->references('id')
                ->on('sales_orders')
                ->cascadeOnDelete();
        });

        // 4. Sales Returns
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('return_number')->unique();
            $table->uuid('sales_order_id');
            $table->string('reason'); // damaged, expired, wrong_item
            $table->decimal('refund_amount', 15, 2);
            $table->string('status')->default('pending_approval'); // pending_approval, completed
            $table->timestamps();

            $table->foreign('sales_order_id')
                ->references('id')
                ->on('sales_orders')
                ->cascadeOnDelete();
        });

        // 5. Sales Return Items
        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sales_return_id');
            $table->uuid('product_id');
            $table->decimal('quantity', 10, 2);
            $table->decimal('refund_unit_price', 15, 2);
            $table->timestamps();

            $table->foreign('sales_return_id')
                ->references('id')
                ->on('sales_returns')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });

        // 6. Credit Notes
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('note_number')->unique();
            $table->uuid('customer_id');
            $table->uuid('sales_return_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('active'); // active, redeemed
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->cascadeOnDelete();

            $table->foreign('sales_return_id')
                ->references('id')
                ->on('sales_returns')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_notes');
        Schema::dropIfExists('sales_return_items');
        Schema::dropIfExists('sales_returns');
        Schema::dropIfExists('sales_invoices');
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_orders');
    }
};
