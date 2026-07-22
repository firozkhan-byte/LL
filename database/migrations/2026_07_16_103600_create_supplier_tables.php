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
        // 1. Suppliers Table
        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('gstin')->nullable();
            $table->string('pan')->nullable();
            $table->integer('payment_terms_days')->default(30); // credit days
            $table->decimal('credit_limit', 15, 2)->default(0.00);
            $table->decimal('rating', 3, 2)->default(5.00); // vendor rating e.g. 4.50
            $table->decimal('outstanding_balance', 15, 2)->default(0.00);
            $table->string('status')->default('pending_approval'); // pending_approval, active, inactive, rejected
            $table->softDeletes();
            $table->timestamps();
        });

        // 2. Supplier Contacts
        Schema::create('supplier_contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('supplier_id');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('designation')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->cascadeOnDelete();
        });

        // 3. Supplier Bank Accounts
        Schema::create('supplier_bank_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('supplier_id');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('ifsc_code');
            $table->string('branch_name')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->cascadeOnDelete();
        });

        // 4. Supplier Documents
        Schema::create('supplier_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('supplier_id');
            $table->string('document_name'); // e.g. GST Certificate, PAN Card, MSME Certificate
            $table->string('file_path');
            $table->timestamps();

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_documents');
        Schema::dropIfExists('supplier_bank_accounts');
        Schema::dropIfExists('supplier_contacts');
        Schema::dropIfExists('suppliers');
    }
};
