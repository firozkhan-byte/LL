<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\InventoryManager;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Product;
use App\Models\RegionalOffice;
use App\Models\StockLedger;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\StockValuationService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InventoryManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Warehouse $warehouse;

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

    public function test_inventory_manager_component_can_be_rendered(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.inventory'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(InventoryManager::class);
    }

    public function test_stock_adjustment_write_off_reduces_ledger_balance(): void
    {
        // 1. Set opening ledger balance (50 units)
        StockLedger::create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'opening',
            'quantity' => 50.00,
            'balance_after' => 50.00,
            'unit_price' => 2000.00,
        ]);

        // 2. Adjust write-off decrement 10 units via Livewire
        Livewire::actingAs($this->adminUser)
            ->test(InventoryManager::class)
            ->set('selectedWarehouseId', $this->warehouse->id)
            ->set('adjReason', 'damaged')
            ->set('adjItems', [
                [
                    'product_id' => $this->product->id,
                    'adjustment_type' => 'decrement',
                    'quantity' => 10.00,
                    'batch_number' => 'BATCH-TEST',
                ],
            ])
            ->call('saveAdjustment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('stock_ledgers', [
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'adjustment_remove',
            'quantity' => -10.00,
            'balance_after' => 40.00,
        ]);
    }

    public function test_fifo_lifo_wac_valuation_engine_logic(): void
    {
        // Add layers:
        // 1. Opening: 10 units @ ₹100
        StockLedger::create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'opening',
            'quantity' => 10.00,
            'balance_after' => 10.00,
            'unit_price' => 100.00,
            'created_at' => now()->subDays(5),
        ]);

        // 2. Purchase: 10 units @ ₹150
        StockLedger::create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'purchase',
            'quantity' => 10.00,
            'balance_after' => 20.00,
            'unit_price' => 150.00,
            'created_at' => now()->subDays(2),
        ]);

        // 3. Sale: -5 units
        StockLedger::create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'sale',
            'quantity' => -5.00,
            'balance_after' => 15.00,
            'unit_price' => 150.00,
            'created_at' => now()->subDays(1),
        ]);

        $service = new StockValuationService;

        // FIFO valuation:
        // 5 units sold from Opening (10 @ ₹100). Remaining: 5 @ ₹100 + 10 @ ₹150 = ₹500 + ₹1500 = ₹2000.
        $this->assertEquals(2000.00, $service->calculateFIFO($this->product->id, $this->warehouse->id));

        // LIFO valuation:
        // 5 units sold from Purchase (10 @ ₹150). Remaining: 10 @ ₹100 + 5 @ ₹150 = ₹1000 + ₹750 = ₹1750.
        $this->assertEquals(1750.00, $service->calculateLIFO($this->product->id, $this->warehouse->id));

        // Weighted Average Costing:
        // Total In: 20 units at total cost 10*100 + 10*150 = ₹2500. Average = ₹125/unit.
        // Balance = 15 units * ₹125 = ₹1875.
        $this->assertEquals(1875.00, $service->calculateWeightedAverage($this->product->id, $this->warehouse->id));
    }
}
