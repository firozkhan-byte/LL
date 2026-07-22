<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $apiUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->apiUser = User::factory()->create();
    }

    public function test_api_products_list_endpoint(): void
    {
        Product::create([
            'name' => 'API Whiskey 750ml',
            'volume_ml' => 750,
            'alcohol_percentage' => 40.0,
            'mrp' => 3000,
            'purchase_price' => 2000,
            'selling_price' => 2800,
            'liquor_type' => 'Spirit',
            'sku' => 'SKU-API-WHI-750',
            'status' => 'active',
        ]);

        Sanctum::actingAs($this->apiUser, ['*']);

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'API Whiskey 750ml',
                'sku' => 'SKU-API-WHI-750',
            ]);
    }

    public function test_api_product_details_endpoint(): void
    {
        $product = Product::create([
            'name' => 'API Beer 330ml',
            'volume_ml' => 330,
            'alcohol_percentage' => 4.5,
            'mrp' => 200,
            'purchase_price' => 150,
            'selling_price' => 180,
            'liquor_type' => 'Beer',
            'sku' => 'SKU-API-BEER-330',
            'status' => 'active',
        ]);

        Sanctum::actingAs($this->apiUser, ['*']);

        $response = $this->getJson("/api/v1/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'API Beer 330ml');
    }
}
