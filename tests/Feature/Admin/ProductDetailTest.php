<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\ProductDetail;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Super Admin');

        $cat = Category::create(['name' => 'Vodka', 'slug' => 'vodka']);
        $brand = Brand::create(['name' => 'Absolut', 'slug' => 'absolut']);

        $this->product = Product::create([
            'name' => 'Absolut Blue',
            'category_id' => $cat->id,
            'brand_id' => $brand->id,
            'volume_ml' => 750,
            'alcohol_percentage' => 40.0,
            'mrp' => 2800,
            'purchase_price' => 2000,
            'selling_price' => 2500,
            'liquor_type' => 'Spirit',
            'sku' => 'SKU-ABSOLUT-750',
            'status' => 'active',
        ]);
    }

    public function test_unauthorized_user_cannot_view_product_detail(): void
    {
        $guestUser = User::factory()->create(); // guest has no Super Admin roles / permission

        $response = $this->actingAs($guestUser)
            ->get(route('admin.products.detail', $this->product->id));

        $response->assertStatus(403);
    }

    public function test_product_detail_component_can_be_rendered(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.products.detail', $this->product->id));

        $response->assertStatus(200);
        $response->assertSeeLivewire(ProductDetail::class);
    }

    public function test_can_toggle_product_status(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(ProductDetail::class, ['id' => $this->product->id])
            ->call('toggleStatus')
            ->assertHasNoErrors();

        $this->product->refresh();
        $this->assertEquals('inactive', $this->product->status);
    }

    public function test_can_switch_tabs(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(ProductDetail::class, ['id' => $this->product->id])
            ->set('activeTab', 'pricing')
            ->assertSet('activeTab', 'pricing')
            ->call('changeTab', 'ledger')
            ->assertSet('activeTab', 'ledger');
    }

    public function test_can_edit_product_directly_from_detail_view(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(ProductDetail::class, ['id' => $this->product->id])
            ->call('openEditModal')
            ->set('name', 'Absolut Citron')
            ->set('sellingPrice', 2700)
            ->call('saveProduct')
            ->assertHasNoErrors();

        $this->product->refresh();
        $this->assertEquals('Absolut Citron', $this->product->name);
        $this->assertEquals(2700, $this->product->selling_price);
    }

    public function test_can_soft_delete_and_restore_product(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(ProductDetail::class, ['id' => $this->product->id])
            ->call('deleteProduct')
            ->assertHasNoErrors();

        $this->assertSoftDeleted('products', ['id' => $this->product->id]);

        Livewire::actingAs($this->adminUser)
            ->test(ProductDetail::class, ['id' => $this->product->id])
            ->call('restoreProduct')
            ->assertHasNoErrors();

        $this->product->refresh();
        $this->assertNull($this->product->deleted_at);
    }
}
