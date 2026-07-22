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
        // 1. Customers (Loyalty registry)
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->string('membership_type')->default('regular'); // regular, silver, gold, platinum
            $table->integer('loyalty_points')->default(0);
            $table->timestamps();
        });

        // 2. Coupons (Discounts)
        Schema::create('coupons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('discount_type'); // percentage, fixed
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_purchase_amount', 15, 2)->default(0.00);
            $table->date('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Gift Cards (Store credit balances)
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('card_number')->unique();
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->date('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. POS Sales orders
        Schema::create('pos_sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('invoice_number')->unique();
            $table->uuid('warehouse_id');
            $table->uuid('customer_id')->nullable();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2);
            $table->string('payment_status')->default('paid'); // paid, partially_paid, unpaid
            $table->json('payment_methods'); // split amounts {"cash": 100, "upi": 200}
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->cascadeOnDelete();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->nullOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        // 5. POS Sale Items lines
        Schema::create('pos_sale_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pos_sale_id');
            $table->uuid('product_id');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('total_price', 15, 2);
            $table->timestamps();

            $table->foreign('pos_sale_id')
                ->references('id')
                ->on('pos_sales')
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
        Schema::dropIfExists('pos_sale_items');
        Schema::dropIfExists('pos_sales');
        Schema::dropIfExists('gift_cards');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('customers');
    }
};
