<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Product;
use App\Models\RegionalOffice;
use App\Models\StockLedger;
use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InventoryApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $apiUser;

    protected Warehouse $warehouse;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->apiUser = User::factory()->create();

        $company = Company::create([
            'name' => 'Living Liquidz API',
            'status' => 'active',
        ]);

        $regionalOffice = RegionalOffice::create([
            'company_id' => $company->id,
            'name' => 'West Regional Office API',
            'code' => 'RO-WEST-API',
        ]);

        $branch = Branch::create([
            'company_id' => $company->id,
            'regional_office_id' => $regionalOffice->id,
            'name' => 'Mumbai Branch API',
            'code' => 'BR-MUM-API',
            'status' => 'active',
        ]);

        $this->warehouse = Warehouse::create([
            'branch_id' => $branch->id,
            'name' => 'Mumbai API Warehouse',
            'code' => 'WH-API-TEST',
            'status' => 'active',
        ]);

        $this->product = Product::create([
            'name' => 'Jacob Creek Shiraz',
            'volume_ml' => 750,
            'alcohol_percentage' => 13.50,
            'mrp' => 1800.00,
            'purchase_price' => 1000.00,
            'selling_price' => 1500.00,
            'liquor_type' => 'Wine',
            'status' => 'active',
        ]);
    }

    public function test_api_inventory_stock_lookup_endpoint(): void
    {
        StockLedger::create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'opening',
            'quantity' => 125,
            'balance_after' => 125,
            'unit_price' => 1000.00,
        ]);

        Sanctum::actingAs($this->apiUser, ['*']);

        $response = $this->getJson("/api/v1/inventory/stock/{$this->product->sku}");

        $response->assertStatus(200)
            ->assertJsonPath('data.available_qty', 125);
    }

    public function test_api_inventory_adjustment_post_endpoint(): void
    {
        StockLedger::create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'opening',
            'quantity' => 100,
            'balance_after' => 100,
            'unit_price' => 1000.00,
        ]);

        Sanctum::actingAs($this->apiUser, ['*']);

        $payload = [
            'warehouse_id' => $this->warehouse->id,
            'reason' => 'damaged',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'adjustment_type' => 'decrement',
                    'quantity' => 15,
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/inventory/adjust', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('stock_ledgers', [
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'adjustment_remove',
            'quantity' => -15.00,
            'balance_after' => 85.00,
        ]);
    }
}
