<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $order = DB::table('sales_orders')->first();

        // 1. Seed Delivery Agent
        $agentId = Str::uuid()->toString();
        DB::table('delivery_agents')->insert([
            'id' => $agentId,
            'name' => 'Amit Sharma',
            'phone' => '9876543210',
            'status' => 'available',
            'vehicle_number' => 'MH-12-AB-1234',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Seed Fleet Vehicle
        $vehId = Str::uuid()->toString();
        DB::table('vehicles')->insert([
            'id' => $vehId,
            'model' => 'Hero Splendor Plus',
            'plate_number' => 'MH-12-AB-1234',
            'type' => 'bike',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Seed Delivery
        if ($order) {
            DB::table('deliveries')->insert([
                'id' => Str::uuid()->toString(),
                'sales_order_id' => $order->id,
                'delivery_agent_id' => $agentId,
                'vehicle_id' => $vehId,
                'status' => 'assigned',
                'otp' => '5421',
                'gps_lat' => 19.0760,
                'gps_lng' => 72.8777,
                'proof_signature' => null,
                'proof_photo_url' => null,
                'estimated_delivery_time' => now()->addMinutes(45),
                'actual_delivery_time' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
