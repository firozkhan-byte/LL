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
        // 1. Delivery Agents
        Schema::create('delivery_agents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('phone');
            $table->string('status')->default('available'); // available, busy, offline
            $table->string('vehicle_number')->nullable();
            $table->timestamps();
        });

        // 2. Fleet Vehicles
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('model');
            $table->string('plate_number')->unique();
            $table->string('type')->default('bike'); // bike, van, truck
            $table->string('status')->default('active'); // active, maintenance
            $table->timestamps();
        });

        // 3. Deliveries logs
        Schema::create('deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sales_order_id');
            $table->uuid('delivery_agent_id')->nullable();
            $table->uuid('vehicle_id')->nullable();
            $table->string('status')->default('assigned'); // assigned, in_transit, delivered, failed
            $table->string('otp');
            $table->decimal('gps_lat', 10, 8)->nullable();
            $table->decimal('gps_lng', 11, 8)->nullable();
            $table->text('proof_signature')->nullable();
            $table->string('proof_photo_url')->nullable();
            $table->timestamp('estimated_delivery_time')->nullable();
            $table->timestamp('actual_delivery_time')->nullable();
            $table->timestamps();

            $table->foreign('sales_order_id')
                ->references('id')
                ->on('sales_orders')
                ->cascadeOnDelete();

            $table->foreign('delivery_agent_id')
                ->references('id')
                ->on('delivery_agents')
                ->nullOnDelete();

            $table->foreign('vehicle_id')
                ->references('id')
                ->on('vehicles')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('delivery_agents');
    }
};
