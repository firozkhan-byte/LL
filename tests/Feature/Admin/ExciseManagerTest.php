<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\ExciseManager;
use App\Models\ExciseLicense;
use App\Models\ExcisePermit;
use App\Models\HsnCode;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExciseManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected ExciseLicense $license;

    protected Supplier $supplier;

    protected Product $product;

    protected HsnCode $hsnCode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Super Admin');

        $this->license = ExciseLicense::create([
            'license_number' => 'LIC-EX-MH-9999',
            'license_type' => 'FL-III',
            'state' => 'Maharashtra',
            'expiry_date' => now()->addDays(15)->format('Y-m-d'),
            'status' => 'active',
            'renewal_fee' => 100000.00,
        ]);

        $this->supplier = Supplier::create([
            'name' => 'Govt Distillery Ltd',
            'contact_name' => 'Official Representative',
            'phone' => '1234567890',
            'email' => 'govt@distill.in',
            'address' => 'Mumbai Depot',
        ]);

        $this->hsnCode = HsnCode::create([
            'code' => '2208',
            'description' => 'Whisky Spirits',
            'gst_rate' => 18.00,
            'excise_duty_rate' => 150.00,
        ]);

        $this->product = Product::create([
            'name' => 'Premium Single Malt Whisky',
            'volume_ml' => 750,
            'alcohol_percentage' => 40.00,
            'mrp' => 5000.00,
            'purchase_price' => 3000.00,
            'selling_price' => 4500.00,
            'hsn_code_id' => $this->hsnCode->id,
            'liquor_type' => 'Spirit',
            'status' => 'active',
        ]);
    }

    public function test_compliance_manager_component_can_be_rendered(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.compliance'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(ExciseManager::class);
    }

    public function test_can_register_new_transit_permit(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(ExciseManager::class)
            ->call('openPermitModal')
            ->set('permitNumber', 'PRM-EX-9988')
            ->set('permitLicenseId', $this->license->id)
            ->set('permitSupplierId', $this->supplier->id)
            ->set('permitIssueDate', '2026-07-16')
            ->set('permitExpiryDate', '2026-08-16')
            ->call('savePermit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('excise_permits', [
            'permit_number' => 'PRM-EX-9988',
            'status' => 'pending',
        ]);
    }

    public function test_can_utilize_excise_permit(): void
    {
        $permit = ExcisePermit::create([
            'permit_number' => 'PRM-EX-7766',
            'excise_license_id' => $this->license->id,
            'supplier_id' => $this->supplier->id,
            'issue_date' => '2026-07-16',
            'expiry_date' => '2026-08-16',
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(ExciseManager::class)
            ->call('utilizePermit', $permit->id)
            ->assertHasNoErrors();

        $this->assertEquals('utilized', $permit->fresh()->status);
    }

    public function test_can_renew_excise_license(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(ExciseManager::class)
            ->call('renewLicense', $this->license->id)
            ->assertHasNoErrors();

        $this->assertEquals('active', $this->license->fresh()->status);
    }

    public function test_can_calculate_daily_excise_registers(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(ExciseManager::class)
            ->call('openRegisterModal')
            ->set('regProductId', $this->product->id)
            ->set('regLicenseId', $this->license->id)
            ->set('regDate', '2026-07-16')
            ->call('triggerRegisterCalculation')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('excise_registers', [
            'product_id' => $this->product->id,
            'opening_balance' => 100.00,
            'closing_balance' => 100.00,
        ]);
    }
}
