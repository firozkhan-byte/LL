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
        // 1. Chart of Accounts
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('type'); // asset, liability, equity, revenue, expense
            $table->uuid('parent_id')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')
                ->on('accounts')
                ->nullOnDelete();
        });

        // 2. Journal Entries
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('entry_date');
            $table->string('reference_number')->unique();
            $table->text('description')->nullable();
            $table->string('status')->default('draft'); // draft, posted
            $table->timestamps();
        });

        // 3. Journal Lines
        Schema::create('journal_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('journal_entry_id');
            $table->uuid('account_id');
            $table->decimal('debit', 15, 2)->default(0.00);
            $table->decimal('credit', 15, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('journal_entry_id')
                ->references('id')
                ->on('journal_entries')
                ->cascadeOnDelete();

            $table->foreign('account_id')
                ->references('id')
                ->on('accounts')
                ->cascadeOnDelete();
        });

        // 4. Budgets
        Schema::create('budgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('account_id');
            $table->integer('fiscal_year');
            $table->decimal('allocated_amount', 15, 2);
            $table->decimal('spent_amount', 15, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('account_id')
                ->references('id')
                ->on('accounts')
                ->cascadeOnDelete();
        });

        // 5. Depreciation Schedules
        Schema::create('depreciation_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('asset_name');
            $table->decimal('purchase_cost', 15, 2);
            $table->decimal('salvage_value', 15, 2);
            $table->integer('useful_life_years');
            $table->string('depreciation_method')->default('straight_line');
            $table->decimal('current_value', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depreciation_schedules');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('journal_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('accounts');
    }
};
