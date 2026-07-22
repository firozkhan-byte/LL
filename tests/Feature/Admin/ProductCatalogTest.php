<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\ProductCatalog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Super Admin');
    }

    public function test_product_catalog_component_can_be_rendered(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.products'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(ProductCatalog::class);
    }

    public function test_product_can_be_created_via_livewire(): void
    {
        $category = Category::create(['name' => 'Whiskey', 'slug' => 'whiskey']);
        $brand = Brand::create(['name' => 'Johnnie Walker', 'slug' => 'johnnie-walker']);

        Livewire::actingAs($this->adminUser)
            ->test(ProductCatalog::class)
            ->set('name', 'Johnnie Walker Double Black')
            ->set('categoryId', $category->id)
            ->set('brandId', $brand->id)
            ->set('volumeMl', 750)
            ->set('alcoholPercentage', 40.0)
            ->set('mrp', 4500)
            ->set('purchasePrice', 3200)
            ->set('sellingPrice', 4200)
            ->set('liquorType', 'Spirit')
            ->call('saveProduct')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'name' => 'Johnnie Walker Double Black',
            'volume_ml' => 750,
            'selling_price' => 4200,
        ]);
    }

    public function test_product_search_and_filters(): void
    {
        $cat1 = Category::create(['name' => 'Whiskey', 'slug' => 'whiskey']);
        $cat2 = Category::create(['name' => 'Beer', 'slug' => 'beer']);

        $p1 = Product::create([
            'name' => 'Black Label',
            'category_id' => $cat1->id,
            'volume_ml' => 750,
            'alcohol_percentage' => 40,
            'mrp' => 3800,
            'purchase_price' => 2800,
            'selling_price' => 3500,
            'liquor_type' => 'Spirit',
            'sku' => 'SKU-BLACK-750',
            'status' => 'active',
        ]);

        $p2 = Product::create([
            'name' => 'Corona Extra',
            'category_id' => $cat2->id,
            'volume_ml' => 330,
            'alcohol_percentage' => 4.5,
            'mrp' => 280,
            'purchase_price' => 190,
            'selling_price' => 250,
            'liquor_type' => 'Beer',
            'sku' => 'SKU-CORONA-330',
            'status' => 'active',
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(ProductCatalog::class)
            ->set('search', 'Black')
            ->assertSee('Black Label')
            ->assertDontSee('Corona Extra');

        Livewire::actingAs($this->adminUser)
            ->test(ProductCatalog::class)
            ->set('selectedCategory', $cat2->id)
            ->assertSee('Corona Extra')
            ->assertDontSee('Black Label');
    }

    public function test_product_can_be_soft_deleted_and_restored(): void
    {
        $product = Product::create([
            'name' => 'Test Brew',
            'volume_ml' => 500,
            'alcohol_percentage' => 5.0,
            'mrp' => 200,
            'purchase_price' => 150,
            'selling_price' => 180,
            'liquor_type' => 'Beer',
            'sku' => 'SKU-BREW-500',
            'status' => 'active',
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(ProductCatalog::class)
            ->call('deleteProduct', $product->id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('products', ['id' => $product->id]);

        Livewire::actingAs($this->adminUser)
            ->test(ProductCatalog::class)
            ->set('filterStatus', 'deleted')
            ->call('restoreProduct', $product->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'deleted_at' => null,
        ]);
    }
}
