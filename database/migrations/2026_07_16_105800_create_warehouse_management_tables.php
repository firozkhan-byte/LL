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
        // 1. Warehouse Racks
        Schema::create('warehouse_racks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('warehouse_id');
            $table->string('code'); // unique per warehouse e.g. RACK-A
            $table->string('name');
            $table->timestamps();

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->cascadeOnDelete();
        });

        // 2. Warehouse Shelves
        Schema::create('warehouse_shelves', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rack_id');
            $table->string('code'); // e.g. SHELF-1
            $table->string('name');
            $table->timestamps();

            $table->foreign('rack_id')
                ->references('id')
                ->on('warehouse_racks')
                ->cascadeOnDelete();
        });

        // 3. Warehouse Bins
        Schema::create('warehouse_bins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('shelf_id');
            $table->string('code')->unique(); // e.g. BIN-A1-S1-01
            $table->string('name');
            $table->decimal('capacity_weight', 8, 2)->default(100.00);
            $table->timestamps();

            $table->foreign('shelf_id')
                ->references('id')
                ->on('warehouse_shelves')
                ->cascadeOnDelete();
        });

        // 4. Bin Inventories
        Schema::create('bin_inventories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bin_id');
            $table->uuid('product_id');
            $table->decimal('quantity', 10, 2)->default(0.00);
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();

            $table->foreign('bin_id')
                ->references('id')
                ->on('warehouse_bins')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });

        // 5. Stock Transfers
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->uuid('from_warehouse_id');
            $table->uuid('to_warehouse_id');
            $table->string('status')->default('pending'); // pending, transit, completed, cancelled
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('from_warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->cascadeOnDelete();

            $table->foreign('to_warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->cascadeOnDelete();
        });

        // 6. Stock Transfer Items
        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('stock_transfer_id');
            $table->uuid('product_id');
            $table->decimal('quantity', 10, 2);
            $table->string('batch_number')->nullable();
            $table->timestamps();

            $table->foreign('stock_transfer_id')
                ->references('id')
                ->on('stock_transfers')
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
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('bin_inventories');
        Schema::dropIfExists('warehouse_bins');
        Schema::dropIfExists('warehouse_shelves');
        Schema::dropIfExists('warehouse_racks');
    }
};
