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
        // 1. HSN Codes
        Schema::create('hsn_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('description');
            $table->decimal('gst_rate', 5, 2)->default(18.00);
            $table->decimal('excise_duty_rate', 5, 2)->default(0.00);
            $table->timestamps();
        });

        // Add HSN reference to products
        Schema::table('products', function (Blueprint $table) {
            $table->uuid('hsn_code_id')->nullable()->after('status');
            $table->foreign('hsn_code_id')
                ->references('id')
                ->on('hsn_codes')
                ->nullOnDelete();
        });

        // 2. Excise Licenses
        Schema::create('excise_licenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('license_number')->unique();
            $table->string('license_type'); // e.g. FL-III, CL-II
            $table->string('state');
            $table->date('expiry_date');
            $table->string('status')->default('active'); // active, expired, pending_renewal
            $table->decimal('renewal_fee', 15, 2)->default(0.00);
            $table->timestamps();
        });

        // 3. Excise Permits
        Schema::create('excise_permits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('permit_number')->unique();
            $table->uuid('excise_license_id');
            $table->uuid('supplier_id');
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->string('status')->default('pending'); // pending, utilized, expired
            $table->timestamps();

            $table->foreign('excise_license_id')
                ->references('id')
                ->on('excise_licenses')
                ->cascadeOnDelete();

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->cascadeOnDelete();
        });

        // 4. Daily Excise Registers
        Schema::create('excise_registers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('transaction_date');
            $table->uuid('excise_license_id');
            $table->uuid('product_id');
            $table->decimal('opening_balance', 10, 2)->default(0.00);
            $table->decimal('received_quantity', 10, 2)->default(0.00);
            $table->decimal('sold_quantity', 10, 2)->default(0.00);
            $table->decimal('closing_balance', 10, 2)->default(0.00);
            $table->decimal('excise_duty_paid', 15, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('excise_license_id')
                ->references('id')
                ->on('excise_licenses')
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
        Schema::dropIfExists('excise_registers');
        Schema::dropIfExists('excise_permits');
        Schema::dropIfExists('excise_licenses');

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['hsn_code_id']);
            $table->dropColumn('hsn_code_id');
        });

        Schema::dropIfExists('hsn_codes');
    }
};
