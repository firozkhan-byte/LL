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
        // 1. Customer Wallets
        Schema::create('customer_wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->string('currency')->default('INR');
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->cascadeOnDelete();
        });

        // 2. Customer Wallet Transactions
        Schema::create('customer_wallet_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_wallet_id');
            $table->string('transaction_type'); // deposit, withdrawal, refund, purchase
            $table->decimal('amount', 15, 2);
            $table->string('reference_type')->nullable();
            $table->uuid('reference_id')->nullable();
            $table->timestamps();

            $table->foreign('customer_wallet_id')
                ->references('id')
                ->on('customer_wallets')
                ->cascadeOnDelete();
        });

        // 3. Customer Profiles
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->date('birthday')->nullable();
            $table->date('anniversary')->nullable();
            $table->json('preferences')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->cascadeOnDelete();
        });

        // 4. CRM Tickets
        Schema::create('crm_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->string('type')->default('support'); // feedback, complaint, support
            $table->string('subject');
            $table->text('description');
            $table->string('status')->default('open'); // open, in_progress, resolved
            $table->string('priority')->default('medium'); // low, medium, high
            $table->uuid('assigned_to')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->cascadeOnDelete();

            $table->foreign('assigned_to')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });

        // 5. CRM Campaigns
        Schema::create('crm_campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('channel')->default('email'); // email, sms, whatsapp
            $table->string('subject')->nullable();
            $table->text('content');
            $table->string('status')->default('draft'); // draft, sent
            $table->integer('sent_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_campaigns');
        Schema::dropIfExists('crm_tickets');
        Schema::dropIfExists('customer_profiles');
        Schema::dropIfExists('customer_wallet_transactions');
        Schema::dropIfExists('customer_wallets');
    }
};
