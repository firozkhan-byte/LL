<?php

namespace Tests\Feature\Api;

use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SupplierApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $apiUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->apiUser = User::factory()->create();
    }

    public function test_api_suppliers_list_endpoint(): void
    {
        Supplier::create([
            'name' => 'API Supplier Active',
            'code' => 'SUP-API-ACT',
            'rating' => 4.5,
            'payment_terms_days' => 30,
            'status' => 'active',
        ]);

        Sanctum::actingAs($this->apiUser, ['*']);

        $response = $this->getJson('/api/v1/suppliers');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'API Supplier Active',
                'code' => 'SUP-API-ACT',
            ]);
    }

    public function test_api_supplier_details_endpoint(): void
    {
        $supplier = Supplier::create([
            'name' => 'API Supplier Detail',
            'code' => 'SUP-API-DTL',
            'rating' => 4.2,
            'payment_terms_days' => 15,
            'status' => 'active',
        ]);

        Sanctum::actingAs($this->apiUser, ['*']);

        $response = $this->getJson("/api/v1/suppliers/{$supplier->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'API Supplier Detail');
    }
}
