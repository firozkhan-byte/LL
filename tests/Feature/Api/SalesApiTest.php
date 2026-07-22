<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Product;
use App\Models\RegionalOffice;
use App\Models\SalesOrder;
use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SalesApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $apiUser;

    protected Warehouse $warehouse;

    protected Product $product;

    protected Customer $customer;

    protected SalesOrder $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->apiUser = User::factory()->create();

        $company = Company::create([
            'name' => 'Living Liquidz',
            'status' => 'active',
        ]);

        $regionalOffice = RegionalOffice::create([
            'company_id' => $company->id,
            'name' => 'West Regional Office',
            'code' => 'RO-WEST',
        ]);

        $branch = Branch::create([
            'company_id' => $company->id,
            'regional_office_id' => $regionalOffice->id,
            'name' => 'Mumbai Branch',
            'code' => 'BR-MUM-01',
            'status' => 'active',
        ]);

        $this->warehouse = Warehouse::create([
            'branch_id' => $branch->id,
            'name' => 'Mumbai Warehouse',
            'code' => 'WH-MUM-TEST',
            'status' => 'active',
        ]);

        $this->product = Product::create([
            'name' => 'Chivas Regal 12Y',
            'volume_ml' => 750,
            'alcohol_percentage' => 40.00,
            'mrp' => 3800.00,
            'purchase_price' => 2500.00,
            'selling_price' => 3200.00,
            'liquor_type' => 'Spirit',
            'status' => 'active',
        ]);

        $this->customer = Customer::create([
            'name' => 'Jane API Doe',
            'phone' => '7777777777',
            'membership_type' => 'regular',
            'loyalty_points' => 0,
        ]);

        $this->order = SalesOrder::create([
            'order_number' => 'SO-API-1111',
            'warehouse_id' => $this->warehouse->id,
            'customer_id' => $this->customer->id,
            'order_type' => 'corporate',
            'status' => 'pending',
            'subtotal' => 3200.00,
            'tax_amount' => 576.00,
            'total_amount' => 3776.00,
        ]);

        $this->order->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 3200.00,
            'total_price' => 3200.00,
        ]);
    }

    public function test_api_track_order_status(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $response = $this->getJson("/api/v1/sales/track/{$this->order->order_number}");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.total_amount', 3776);
    }

    public function test_api_create_corporate_order(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $payload = [
            'warehouse_id' => $this->warehouse->id,
            'customer_id' => $this->customer->id,
            'order_type' => 'corporate',
            'delivery_address' => 'API Delivery Hub, Mumbai',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'unit_price' => 3200.00,
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/sales/corporate', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['order_id', 'order_number', 'total_amount']]);
    }
}
