<?php

namespace Tests\Feature\Api;

use App\Models\BinInventory;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Product;
use App\Models\RegionalOffice;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseBin;
use App\Models\WarehouseRack;
use App\Models\WarehouseShelf;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WarehouseApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $apiUser;

    protected WarehouseBin $bin;

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

        $warehouse = Warehouse::create([
            'branch_id' => $branch->id,
            'name' => 'Mumbai API Warehouse',
            'code' => 'WH-API-TEST',
            'status' => 'active',
        ]);

        $rack = WarehouseRack::create([
            'warehouse_id' => $warehouse->id,
            'code' => 'R-API',
            'name' => 'Rack API',
        ]);

        $shelf = WarehouseShelf::create([
            'rack_id' => $rack->id,
            'code' => 'S-API',
            'name' => 'Shelf API',
        ]);

        $this->bin = WarehouseBin::create([
            'shelf_id' => $shelf->id,
            'code' => 'BIN-API-123',
            'name' => 'Bin API 123',
            'capacity_weight' => 150,
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

    public function test_api_bin_details_lookup_endpoint(): void
    {
        BinInventory::create([
            'bin_id' => $this->bin->id,
            'product_id' => $this->product->id,
            'quantity' => 45,
        ]);

        Sanctum::actingAs($this->apiUser, ['*']);

        $response = $this->getJson("/api/v1/warehouse/bins/{$this->bin->code}");

        $response->assertStatus(200)
            ->assertJsonPath('data.code', 'BIN-API-123');
    }

    public function test_api_stock_adjust_post_endpoint(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $payload = [
            'bin_id' => $this->bin->id,
            'product_id' => $this->product->id,
            'quantity' => 15,
            'batch_number' => 'BATCH-API-ADJ',
        ];

        $response = $this->postJson('/api/v1/warehouse/adjust', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('bin_inventories', [
            'bin_id' => $this->bin->id,
            'product_id' => $this->product->id,
            'quantity' => 15,
        ]);
    }
}
