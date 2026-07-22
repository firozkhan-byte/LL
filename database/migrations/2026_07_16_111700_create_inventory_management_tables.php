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
        // 1. Stock Ledgers
        Schema::create('stock_ledgers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('warehouse_id')->nullable();
            $table->string('transaction_type'); // opening, purchase, sale, adjustment_add, adjustment_remove, return
            $table->decimal('quantity', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->decimal('unit_price', 15, 2)->default(0.00);
            $table->string('reference_type')->nullable(); // e.g. GoodsReceiptNote, StockAdjustment
            $table->uuid('reference_id')->nullable();
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->cascadeOnDelete();
        });

        // 2. Stock Adjustments
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->uuid('warehouse_id');
            $table->string('reason'); // damaged, lost, cycle_count, theft, write_off
            $table->string('status')->default('completed'); // draft, completed, rejected
            $table->uuid('created_by');
            $table->uuid('approved_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->cascadeOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });

        // 3. Stock Adjustment Items
        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('stock_adjustment_id');
            $table->uuid('product_id');
            $table->string('adjustment_type'); // increment, decrement
            $table->decimal('quantity', 10, 2);
            $table->string('batch_number')->nullable();
            $table->timestamps();

            $table->foreign('stock_adjustment_id')
                ->references('id')
                ->on('stock_adjustments')
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
        Schema::dropIfExists('stock_adjustment_items');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock_ledgers');
    }
};
