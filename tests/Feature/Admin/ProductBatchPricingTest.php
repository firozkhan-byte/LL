<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\POSTerminal;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Tests\TestCase;

class ProductBatchPricingTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Product $product;
    protected Warehouse $warehouse;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Super Admin');

        // Create warehouse
        $this->warehouse = Warehouse::create([
            'branch_id' => \App\Models\Branch::factory()->create()->id,
            'name' => 'Main Warehouse',
            'code' => 'WH-MAIN',
            'status' => 'active',
        ]);

        $cat = Category::create(['name' => 'Rum', 'slug' => 'rum']);
        $brand = Brand::create(['name' => 'Bacardi', 'slug' => 'bacardi']);

        $this->product = Product::create([
            'name' => 'Bacardi Carta Blanca',
            'category_id' => $cat->id,
            'brand_id' => $brand->id,
            'volume_ml' => 750,
            'alcohol_percentage' => 40.0,
            'mrp' => 1800,
            'purchase_price' => 1200,
            'selling_price' => 1600,
            'liquor_type' => 'Spirit',
            'sku' => 'SKU-BACARDI-750',
            'status' => 'active',
        ]);
    }

    public function test_pos_cart_uses_default_selling_price_when_no_active_batches(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(POSTerminal::class)
            ->call('addToCart', $this->product->id)
            ->assertSet('cart.0.price', 1600.00)
            ->assertSet('cart.0.batch_number', null);
    }

    public function test_pos_cart_uses_oldest_batch_pricing_override_when_active_batches_exist(): void
    {
        // Batch 1 (older, cheaper)
        ProductBatch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-001',
            'expiry_date' => now()->addMonths(6),
            'mrp' => 1700,
            'purchase_price' => 1100,
            'selling_price' => 1500,
            'status' => 'active',
        ]);

        // Batch 2 (newer, normal price)
        ProductBatch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-002',
            'expiry_date' => now()->addMonths(12),
            'mrp' => 1800,
            'purchase_price' => 1200,
            'selling_price' => 1600,
            'status' => 'active',
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(POSTerminal::class)
            ->call('addToCart', $this->product->id)
            ->assertSet('cart.0.price', 1500.00) // Uses cheap oldest FIFO batch price
            ->assertSet('cart.0.batch_number', 'BATCH-001');
    }

    public function test_expiry_markdown_engine_artisan_command(): void
    {
        // Expiring Batch (expires in 15 days)
        $expiringBatch = ProductBatch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-EXP',
            'expiry_date' => now()->addDays(15),
            'mrp' => 1800,
            'purchase_price' => 1200,
            'selling_price' => 1600,
            'status' => 'active',
        ]);

        // Far Expiry Batch (expires in 60 days - should NOT be marked down)
        $farBatch = ProductBatch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'BATCH-FAR',
            'expiry_date' => now()->addDays(60),
            'mrp' => 1800,
            'purchase_price' => 1200,
            'selling_price' => 1600,
            'status' => 'active',
        ]);

        // Run the markdown command
        Artisan::call('excise:markdown-expiring-batches', [
            '--days' => 30,
            '--discount' => 20,
        ]);

        $expiringBatch->refresh();
        $farBatch->refresh();

        // Expiring batch should be marked down by 20% (1600 * 0.8 = 1280)
        $this->assertTrue($expiringBatch->is_markdown);
        $this->assertEquals(20.00, $expiringBatch->markdown_percent);
        $this->assertEquals(1280.00, $expiringBatch->selling_price);

        // Far batch should remain untouched
        $this->assertFalse($farBatch->is_markdown);
        $this->assertEquals(1600.00, $farBatch->selling_price);
    }
}
