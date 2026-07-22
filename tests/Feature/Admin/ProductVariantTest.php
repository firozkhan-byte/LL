<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\ProductCatalog;
use App\Livewire\Admin\ProductDetail;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductVariantTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Product $parentProduct;
    protected Product $variantProduct;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Super Admin');

        $cat = Category::create(['name' => 'Whisky', 'slug' => 'whisky']);
        $brand = Brand::create(['name' => 'Chivas Regal', 'slug' => 'chivas-regal']);

        // Parent Product
        $this->parentProduct = Product::create([
            'name' => 'Chivas Regal 12 Yo',
            'category_id' => $cat->id,
            'brand_id' => $brand->id,
            'volume_ml' => 750,
            'alcohol_percentage' => 40.0,
            'mrp' => 6000,
            'purchase_price' => 4500,
            'selling_price' => 5500,
            'liquor_type' => 'Spirit',
            'sku' => 'SKU-CHIVAS-750',
            'status' => 'active',
        ]);

        // Variant Product (Pint size)
        $this->variantProduct = Product::create([
            'parent_id' => $this->parentProduct->id,
            'name' => 'Chivas Regal 12 Yo 375ml',
            'category_id' => $cat->id,
            'brand_id' => $brand->id,
            'volume_ml' => 375,
            'alcohol_percentage' => 40.0,
            'mrp' => 3200,
            'purchase_price' => 2400,
            'selling_price' => 2900,
            'liquor_type' => 'Spirit',
            'sku' => 'SKU-CHIVAS-375',
            'status' => 'active',
        ]);
    }

    public function test_catalog_hides_variants_by_default(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(ProductCatalog::class)
            ->assertSee('Chivas Regal 12 Yo')
            ->assertDontSee('Chivas Regal 12 Yo 375ml');
    }

    public function test_variants_tab_lists_variants_on_parent_detail_page(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(ProductDetail::class, ['id' => $this->parentProduct->id])
            ->set('activeTab', 'variants')
            ->assertSee('Chivas Regal 12 Yo 375ml')
            ->assertSee('SKU-CHIVAS-375');
    }

    public function test_parent_link_banner_visible_on_variant_detail_page(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.products.detail', $this->variantProduct->id));

        $response->assertStatus(200);
        $response->assertSee('This is a variant size of the main product');
        $response->assertSee('View Parent Template');
    }

    public function test_can_add_variant_from_parent_detail_page(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(ProductDetail::class, ['id' => $this->parentProduct->id])
            ->call('openVariantModal')
            ->set('varVolumeMl', 180)
            ->set('varMrp', 1600)
            ->set('varPurchasePrice', 1200)
            ->set('varSellingPrice', 1450)
            ->set('varSku', 'SKU-CHIVAS-180')
            ->call('saveVariant')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'parent_id' => $this->parentProduct->id,
            'volume_ml' => 180,
            'sku' => 'SKU-CHIVAS-180',
            'selling_price' => 1450,
        ]);
    }
}
