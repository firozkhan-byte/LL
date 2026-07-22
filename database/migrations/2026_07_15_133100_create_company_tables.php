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
        // 1. Companies Table
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('registration_number')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->string('logo_path')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 2. Company Settings Table
        Schema::create('company_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id')->unique();
            $table->string('currency')->default('INR');
            $table->string('timezone')->default('Asia/Kolkata');
            $table->string('fiscal_year_start')->default('04-01'); // April 1st
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('India');
            $table->json('settings')->nullable(); // JSON fields for extensible configs
            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->cascadeOnDelete();
        });

        // 3. Regional Offices Table
        Schema::create('regional_offices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->string('name');
            $table->string('code')->unique();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->cascadeOnDelete();
        });

        // 4. Branches Table
        Schema::create('branches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->uuid('regional_office_id')->nullable();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('status')->default('pending_approval'); // pending_approval, active, inactive
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->cascadeOnDelete();

            $table->foreign('regional_office_id')
                ->references('id')
                ->on('regional_offices')
                ->nullOnDelete();
        });

        // 5. Departments Table
        Schema::create('departments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->string('name');
            $table->string('code');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->cascadeOnDelete();
        });

        // 6. Business Units Table
        Schema::create('business_units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->string('name');
            $table->string('code');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->cascadeOnDelete();
        });

        // 7. Cost Centers Table
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->uuid('business_unit_id')->nullable();
            $table->string('name');
            $table->string('code')->unique();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->cascadeOnDelete();

            $table->foreign('business_unit_id')
                ->references('id')
                ->on('business_units')
                ->nullOnDelete();
        });

        // 8. Stores Table
        Schema::create('stores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->string('name');
            $table->string('code')->unique();
            $table->string('license_number')->nullable();
            $table->string('status')->default('pending_approval'); // pending_approval, active, inactive
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->cascadeOnDelete();
        });

        // 9. Warehouses Table
        Schema::create('warehouses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->string('name');
            $table->string('code')->unique();
            $table->string('status')->default('pending_approval'); // pending_approval, active, inactive
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->cascadeOnDelete();
        });

        // 10. Approvals Table (For Branch/Store/Warehouse creation and changes workflow)
        Schema::create('approvals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('approvable_type')->nullable(); // Branch, Store, Warehouse
            $table->uuid('approvable_id')->nullable();
            $table->string('action'); // create, update, delete
            $table->json('data'); // Request details (JSON format payload)
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->uuid('requested_by');
            $table->uuid('approved_by')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->foreign('requested_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('stores');
        Schema::dropIfExists('cost_centers');
        Schema::dropIfExists('business_units');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('regional_offices');
        Schema::dropIfExists('company_settings');
        Schema::dropIfExists('companies');
    }
};
