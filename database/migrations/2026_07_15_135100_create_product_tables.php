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
        // 1. Categories Table (Supporting sub-categories via parent_id)
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')
                ->on('categories')
                ->nullOnDelete();
        });

        // 2. Brands Table
        Schema::create('brands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->softDeletes();
            $table->timestamps();
        });

        // 3. Manufacturers Table
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        // 4. Products Table
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('category_id')->nullable();
            $table->uuid('brand_id')->nullable();
            $table->uuid('manufacturer_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('hsn_code')->nullable();
            $table->decimal('gst_rate', 5, 2)->default(18.00);
            $table->string('liquor_type'); // Spirit, Beer, Wine, Liqueur, Cider, Brandy, etc.
            $table->integer('volume_ml'); // e.g. 750, 375, 1000
            $table->decimal('alcohol_percentage', 5, 2); // e.g. 42.80, 12.50
            $table->decimal('mrp', 10, 2);
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->string('origin_country')->nullable();
            $table->string('origin_region')->nullable();
            $table->boolean('expiry_tracking')->default(false);
            $table->boolean('batch_tracking')->default(false);
            $table->boolean('serial_tracking')->default(false);
            $table->text('description')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->json('attributes')->nullable(); // Dynamic specifications
            $table->json('tags')->nullable(); // Tag lists
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->nullOnDelete();

            $table->foreign('brand_id')
                ->references('id')
                ->on('brands')
                ->nullOnDelete();

            $table->foreign('manufacturer_id')
                ->references('id')
                ->on('manufacturers')
                ->nullOnDelete();
        });

        // 5. Product Images Table
        Schema::create('product_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->string('image_path');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

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
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('manufacturers');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
    }
};
