<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\SalesManager;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Product;
use App\Models\RegionalOffice;
use App\Models\SalesOrder;
use App\Models\StockLedger;
use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SalesManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Warehouse $warehouse;

    protected Product $product;

    protected Customer $customer;

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
            'name' => 'Johnnie Walker Double Black',
            'volume_ml' => 750,
            'alcohol_percentage' => 40.00,
            'mrp' => 4500.00,
            'purchase_price' => 3000.00,
            'selling_price' => 4000.00,
            'liquor_type' => 'Spirit',
            'status' => 'active',
        ]);

        // Seed stock ledger balance
        StockLedger::create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'opening',
            'quantity' => 100,
            'balance_after' => 100,
            'unit_price' => 3000.00,
        ]);

        $this->customer = Customer::create([
            'name' => 'Jane Doe',
            'phone' => '8888888888',
            'membership_type' => 'regular',
            'loyalty_points' => 0,
        ]);
    }

    public function test_sales_manager_component_can_be_rendered(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.sales'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(SalesManager::class);
    }

    public function test_can_create_sales_order_and_record_ledger_deduction(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(SalesManager::class)
            ->call('openCreateModal')
            ->set('newWarehouseId', $this->warehouse->id)
            ->set('newOrderType', 'corporate')
            ->set('newCustomerId', $this->customer->id)
            ->set('newOrderItems', [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 5,
                    'unit_price' => 4000.00,
                ],
            ])
            ->call('saveOrder')
            ->assertHasNoErrors();

        // Check Sales Order database record
        $this->assertDatabaseHas('sales_orders', [
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'order_type' => 'corporate',
            'status' => 'pending',
            'total_amount' => 23600.00, // 5 * 4000 = 20000 + 18% GST = 23600
        ]);

        // Check stock ledger decrement row
        $this->assertDatabaseHas('stock_ledgers', [
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'sale',
            'quantity' => -5.00,
            'balance_after' => 95.00,
        ]);
    }

    public function test_delivered_transition_generates_sales_invoice(): void
    {
        $order = SalesOrder::create([
            'warehouse_id' => $this->warehouse->id,
            'customer_id' => $this->customer->id,
            'order_type' => 'online',
            'status' => 'processing',
            'subtotal' => 4000.00,
            'tax_amount' => 720.00,
            'total_amount' => 4720.00,
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(SalesManager::class)
            ->call('updateOrderStatus', $order->id, 'delivered')
            ->assertHasNoErrors();

        // Verify status updated and invoice generated
        $this->assertEquals('delivered', $order->fresh()->status);
        $this->assertDatabaseHas('sales_invoices', [
            'sales_order_id' => $order->id,
            'total_amount' => 4720.00,
            'status' => 'completed',
        ]);
    }

    public function test_can_process_customer_return_with_credit_notes_and_ledger_reversal(): void
    {
        // 1. Create order
        $order = SalesOrder::create([
            'warehouse_id' => $this->warehouse->id,
            'customer_id' => $this->customer->id,
            'order_type' => 'online',
            'status' => 'delivered',
            'subtotal' => 4000.00,
            'tax_amount' => 720.00,
            'total_amount' => 4720.00,
        ]);

        $orderItem = $order->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 4000.00,
            'total_price' => 4000.00,
        ]);

        // 2. Open return modal and submit return
        Livewire::actingAs($this->adminUser)
            ->test(SalesManager::class)
            ->call('openReturnModal', $order->id)
            ->set('returnReason', 'damaged')
            ->set('returnItems', [
                [
                    'product_id' => $this->product->id,
                    'product_name' => $this->product->name,
                    'purchased_qty' => 1,
                    'quantity_to_return' => 1,
                    'refund_unit_price' => 4000.00,
                ],
            ])
            ->call('saveReturn')
            ->assertHasNoErrors();

        // 3. Check database has sales returns, credit note, and stock ledger refund
        $this->assertDatabaseHas('sales_returns', [
            'sales_order_id' => $order->id,
            'reason' => 'damaged',
            'refund_amount' => 4720.00,
        ]);

        $this->assertDatabaseHas('credit_notes', [
            'customer_id' => $this->customer->id,
            'amount' => 4720.00,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('stock_ledgers', [
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'return',
            'quantity' => 1.00,
            'balance_after' => 101.00, // opening was 100
        ]);
    }
}
