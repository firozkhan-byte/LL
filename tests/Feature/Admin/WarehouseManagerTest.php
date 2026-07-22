<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\WarehouseManager;
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
use Livewire\Livewire;
use Tests\TestCase;

class WarehouseManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Warehouse $warehouse;

    protected WarehouseBin $bin;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Super Admin');

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

        $rack = WarehouseRack::create([
            'warehouse_id' => $this->warehouse->id,
            'code' => 'R1',
            'name' => 'Rack 1',
        ]);

        $shelf = WarehouseShelf::create([
            'rack_id' => $rack->id,
            'code' => 'S1',
            'name' => 'Shelf 1',
        ]);

        $this->bin = WarehouseBin::create([
            'shelf_id' => $shelf->id,
            'code' => 'BIN-TEST-A1',
            'name' => 'Bin A1',
            'capacity_weight' => 100,
        ]);

        $this->product = Product::create([
            'name' => 'JW Black Label',
            'volume_ml' => 750,
            'alcohol_percentage' => 42.80,
            'mrp' => 3500.00,
            'purchase_price' => 2000.00,
            'selling_price' => 3000.00,
            'liquor_type' => 'Spirit',
            'status' => 'active',
        ]);
    }

    public function test_warehouse_manager_component_can_be_rendered(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.warehouse'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(WarehouseManager::class);
    }

    public function test_putaway_stock_reallocation_success(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(WarehouseManager::class)
            ->set('putawayBinId', $this->bin->id)
            ->set('putawayProductId', $this->product->id)
            ->set('putawayQuantity', 25)
            ->call('executePutaway')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('bin_inventories', [
            'bin_id' => $this->bin->id,
            'product_id' => $this->product->id,
            'quantity' => 25,
        ]);
    }

    public function test_cycle_counting_discrepancy_adjustment_success(): void
    {
        $inv = BinInventory::create([
            'bin_id' => $this->bin->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(WarehouseManager::class)
            ->call('adjustCycleDiscrepancy', $inv->id, 8)
            ->assertHasNoErrors();

        $this->assertEquals(8, $inv->fresh()->quantity);
    }

    public function test_stock_transfer_creation_success(): void
    {
        $destination = Warehouse::create([
            'branch_id' => $this->warehouse->branch_id,
            'name' => 'Pune Warehouse',
            'code' => 'WH-PUN-TEST',
            'status' => 'active',
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(WarehouseManager::class)
            ->set('txFromWarehouseId', $this->warehouse->id)
            ->set('txToWarehouseId', $destination->id)
            ->set('txItems', [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 10,
                    'batch' => 'BATCH-TEST',
                ],
            ])
            ->call('saveTransfer')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('stock_transfers', [
            'from_warehouse_id' => $this->warehouse->id,
            'to_warehouse_id' => $destination->id,
            'status' => 'completed',
        ]);
    }
}
