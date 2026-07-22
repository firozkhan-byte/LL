<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\BrandRegistration;
use App\Models\Category;
use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Branch;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExciseBrandRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected InventoryService $inventoryService;
    protected Product $product;
    protected Warehouse $warehouseMH; // Warehouse in Maharashtra
    protected Warehouse $warehouseGoa; // Warehouse in Goa
    protected Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inventoryService = app(InventoryService::class);

        // Seed Company & Settings
        $company = Company::create(['name' => 'Test Company']);
        CompanySetting::create([
            'company_id' => $company->id,
            'state' => 'Maharashtra',
            'settings' => ['enable_excise_checks' => true]
        ]);

        // Seed Branches
        $branchMH = Branch::create(['company_id' => $company->id, 'name' => 'Mumbai Branch', 'code' => 'BR-MUM-01']);
        $branchGoa = Branch::create(['company_id' => $company->id, 'name' => 'Goa Branch', 'code' => 'BR-GOA-01']);

        // Seed Warehouses
        $this->warehouseMH = Warehouse::create(['branch_id' => $branchMH->id, 'name' => 'Mumbai Central Warehouse', 'code' => 'WH-MUM-CTR']);
        $this->warehouseGoa = Warehouse::create(['branch_id' => $branchGoa->id, 'name' => 'Goa Depot', 'code' => 'WH-GOA-DEP']);

        // Seed Brand and Category
        $this->brand = Brand::create(['name' => 'Smirnoff', 'slug' => 'smirnoff']);
        $cat = Category::create(['name' => 'Vodka', 'slug' => 'vodka']);

        // Seed Product
        $this->product = Product::create([
            'name' => 'Smirnoff Red',
            'category_id' => $cat->id,
            'brand_id' => $this->brand->id,
            'volume_ml' => 750,
            'alcohol_percentage' => 37.5,
            'mrp' => 1500,
            'purchase_price' => 1000,
            'selling_price' => 1300,
            'liquor_type' => 'Spirit',
            'sku' => 'SKU-SMIRNOFF-750',
        ]);
    }

    public function test_compliance_succeeds_when_active_registration_exists(): void
    {
        // Maharashtra registration
        BrandRegistration::create([
            'brand_id' => $this->brand->id,
            'state' => 'Maharashtra',
            'excise_code' => 'EX-CODE-SM-MH',
            'expiry_date' => now()->addMonth(),
            'status' => 'active',
            'registration_fee' => 50000,
        ]);

        $result = $this->inventoryService->verifyExciseCompliance($this->product->id, $this->warehouseMH->id);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('Compliance verified', $result['message']);
    }

    public function test_compliance_fails_when_no_registration_exists(): void
    {
        // No registration in Maharashtra (but registered in Goa)
        BrandRegistration::create([
            'brand_id' => $this->brand->id,
            'state' => 'Goa',
            'excise_code' => 'EX-CODE-SM-GOA',
            'expiry_date' => now()->addMonth(),
            'status' => 'active',
            'registration_fee' => 10000,
        ]);

        $result = $this->inventoryService->verifyExciseCompliance($this->product->id, $this->warehouseMH->id);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('not registered or registration has expired', $result['message']);
    }

    public function test_compliance_fails_when_registration_has_expired(): void
    {
        // Expired registration in Maharashtra
        BrandRegistration::create([
            'brand_id' => $this->brand->id,
            'state' => 'Maharashtra',
            'excise_code' => 'EX-CODE-SM-MH',
            'expiry_date' => now()->subDay(), // expired yesterday
            'status' => 'active',
            'registration_fee' => 50000,
        ]);

        $result = $this->inventoryService->verifyExciseCompliance($this->product->id, $this->warehouseMH->id);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('not registered or registration has expired', $result['message']);
    }

    public function test_compliance_skips_when_product_has_no_brand(): void
    {
        $unbrandedProduct = Product::create([
            'name' => 'Unbranded Local Spirit',
            'volume_ml' => 750,
            'alcohol_percentage' => 42.8,
            'mrp' => 300,
            'purchase_price' => 200,
            'selling_price' => 250,
            'liquor_type' => 'Spirit',
            'sku' => 'SKU-UNBRANDED-750',
        ]);

        $result = $this->inventoryService->verifyExciseCompliance($unbrandedProduct->id, $this->warehouseMH->id);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('Product has no brand', $result['message']);
    }
}
