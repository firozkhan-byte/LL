<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\POSTerminal;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Product;
use App\Models\RegionalOffice;
use App\Models\StockLedger;
use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class POSTerminalTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Warehouse $warehouse;

    protected Product $product;

    protected Customer $customer;

    protected Coupon $coupon;

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

        // Seed stock so it can be decremented
        StockLedger::create([
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'opening',
            'quantity' => 100,
            'balance_after' => 100,
            'unit_price' => 2000.00,
        ]);

        $this->customer = Customer::create([
            'name' => 'Test Customer',
            'phone' => '9999999999',
            'membership_type' => 'regular',
            'loyalty_points' => 10,
        ]);

        $this->coupon = Coupon::create([
            'code' => 'SAVE200',
            'discount_type' => 'fixed',
            'discount_value' => 200,
            'min_purchase_amount' => 1000,
            'is_active' => true,
        ]);
    }

    public function test_pos_terminal_component_can_be_rendered(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.pos'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(POSTerminal::class);
    }

    public function test_add_to_cart_and_coupon_application(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(POSTerminal::class)
            ->call('addToCart', $this->product->id)
            ->set('couponCode', 'SAVE200')
            ->call('applyCoupon')
            ->assertSet('discountAmount', 200.00)
            ->assertHasNoErrors();
    }

    public function test_checkout_completes_with_split_payments_and_deducts_stock(): void
    {
        // Cart subtotal: ₹3000 (1 unit of JW Black Label)
        // GST Tax (18% inclusive simulation): ₹3540 total final due.
        // We split it: Cash ₹1540 and UPI ₹2000.

        Livewire::actingAs($this->adminUser)
            ->test(POSTerminal::class)
            ->set('selectedWarehouseId', $this->warehouse->id)
            ->call('addToCart', $this->product->id)
            ->set('customerPhone', '9999999999')
            ->call('lookupCustomer')
            ->call('openCheckout')
            ->set('cashPaid', 1540.00)
            ->set('upiPaid', 2000.00)
            ->set('cardPaid', 0.00)
            ->call('saveCheckout')
            ->assertHasNoErrors();

        // Check POS Sale recorded
        $this->assertDatabaseHas('pos_sales', [
            'customer_id' => $this->customer->id,
            'total_amount' => 3540.00,
        ]);

        // Check Inventory Ledger decremented stock correctly
        $this->assertDatabaseHas('stock_ledgers', [
            'product_id' => $this->product->id,
            'warehouse_id' => $this->warehouse->id,
            'transaction_type' => 'sale',
            'quantity' => -1.00,
            'balance_after' => 99.00,
        ]);
    }
}
