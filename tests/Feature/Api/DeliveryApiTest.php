<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\Delivery;
use App\Models\DeliveryAgent;
use App\Models\SalesOrder;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Warehouse;
use Database\Seeders\CompanySeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\WarehouseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeliveryApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $apiUser;

    protected SalesOrder $order;

    protected DeliveryAgent $agent;

    protected Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(CompanySeeder::class);
        $this->seed(WarehouseSeeder::class);
        $this->apiUser = User::factory()->create();

        $warehouse = Warehouse::first();

        $customer = Customer::create([
            'name' => 'John Delivery Customer',
            'phone' => '9777700000',
            'membership_type' => 'regular',
        ]);

        $this->order = SalesOrder::create([
            'order_number' => 'SO-2026-9999',
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'order_type' => 'online',
            'status' => 'processing',
            'shipping_status' => 'pending',
            'subtotal' => 4000.00,
            'tax_amount' => 1000.00,
            'total_amount' => 5000.00,
        ]);

        $this->agent = DeliveryAgent::create([
            'name' => 'Ramesh Delivery Boy',
            'phone' => '9123456789',
            'vehicle_number' => 'MH-12-XX-9999',
            'status' => 'available',
        ]);

        $this->vehicle = Vehicle::create([
            'model' => 'Honda Activa',
            'plate_number' => 'MH-12-XX-9999',
            'type' => 'bike',
            'status' => 'active',
        ]);
    }

    public function test_api_get_assigned_deliveries(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        Delivery::create([
            'sales_order_id' => $this->order->id,
            'delivery_agent_id' => $this->agent->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => 'assigned',
            'otp' => '9988',
        ]);

        $response = $this->getJson("/api/v1/delivery/assigned?agent_id={$this->agent->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => [['sales_order_id', 'status', 'otp']]]);
    }

    public function test_api_update_gps_location(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $delivery = Delivery::create([
            'sales_order_id' => $this->order->id,
            'delivery_agent_id' => $this->agent->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => 'in_transit',
            'otp' => '9988',
        ]);

        $payload = [
            'lat' => 19.0760,
            'lng' => 72.8777,
        ];

        $response = $this->postJson("/api/v1/delivery/{$delivery->id}/gps", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_api_confirm_otp_checkout(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $delivery = Delivery::create([
            'sales_order_id' => $this->order->id,
            'delivery_agent_id' => $this->agent->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => 'in_transit',
            'otp' => '5678',
        ]);

        $payload = [
            'otp' => '5678',
            'signature' => 'Received by customer',
            'photo_url' => 'https://liquorerp.in/proofs/123.png',
        ];

        $response = $this->postJson("/api/v1/delivery/{$delivery->id}/checkout", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
