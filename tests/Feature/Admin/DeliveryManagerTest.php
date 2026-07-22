<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\DeliveryManager;
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
use Livewire\Livewire;
use Tests\TestCase;

class DeliveryManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected SalesOrder $order;

    protected DeliveryAgent $agent;

    protected Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(CompanySeeder::class);
        $this->seed(WarehouseSeeder::class);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Super Admin');

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

    public function test_delivery_manager_component_can_be_rendered(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.delivery'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(DeliveryManager::class);
    }

    public function test_can_register_new_agent_and_vehicle(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(DeliveryManager::class)
            ->call('openAgentModal')
            ->set('agentName', 'Suresh Kumar')
            ->set('agentPhone', '9222200000')
            ->set('agentVehicle', 'MH-12-YY-8888')
            ->call('saveAgent')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('delivery_agents', [
            'name' => 'Suresh Kumar',
            'vehicle_number' => 'MH-12-YY-8888',
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(DeliveryManager::class)
            ->call('openVehicleModal')
            ->set('vehicleModel', 'Mahindra Bolero')
            ->set('vehiclePlate', 'MH-12-YY-8888')
            ->set('vehicleType', 'van')
            ->call('saveVehicle')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('vehicles', [
            'model' => 'Mahindra Bolero',
            'plate_number' => 'MH-12-YY-8888',
        ]);
    }

    public function test_can_dispatch_sales_order(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(DeliveryManager::class)
            ->call('openDispatchModal', $this->order->id)
            ->set('dispatchAgentId', $this->agent->id)
            ->set('dispatchVehicleId', $this->vehicle->id)
            ->call('dispatchOrder')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('deliveries', [
            'sales_order_id' => $this->order->id,
            'delivery_agent_id' => $this->agent->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => 'assigned',
        ]);
    }

    public function test_can_verify_delivery_via_otp(): void
    {
        $delivery = Delivery::create([
            'sales_order_id' => $this->order->id,
            'delivery_agent_id' => $this->agent->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => 'in_transit',
            'otp' => '1234',
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(DeliveryManager::class)
            ->call('openCompleteModal', $delivery->id)
            ->set('completeOtp', '1234')
            ->set('completeSignature', 'Customer Signature')
            ->call('confirmDelivery')
            ->assertHasNoErrors();

        $this->assertEquals('delivered', $delivery->fresh()->status);
        $this->assertEquals('delivered', $this->order->fresh()->status);
    }
}
