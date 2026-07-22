<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PurchaseApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $apiUser;

    protected Product $product;

    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->apiUser = User::factory()->create();

        $this->product = Product::create([
            'name' => 'Corona Extra Beer',
            'volume_ml' => 330,
            'alcohol_percentage' => 4.50,
            'mrp' => 250.00,
            'purchase_price' => 150.00,
            'selling_price' => 200.00,
            'liquor_type' => 'Beer',
            'status' => 'active',
        ]);

        $this->supplier = Supplier::create([
            'name' => 'Anheuser-Busch InBev',
            'code' => 'SUP-AB-INBEV',
            'outstanding_balance' => 0.00,
            'status' => 'active',
        ]);
    }

    public function test_api_purchase_orders_index_endpoint(): void
    {
        PurchaseOrder::create([
            'supplier_id' => $this->supplier->id,
            'po_date' => now()->toDateString(),
            'code' => 'PO-API-TEST',
            'status' => 'approved',
            'total_amount' => 50000.00,
        ]);

        Sanctum::actingAs($this->apiUser, ['*']);

        $response = $this->getJson('/api/v1/purchase/orders');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'code' => 'PO-API-TEST',
                'status' => 'approved',
            ]);
    }

    public function test_api_scanner_can_post_grn(): void
    {
        $po = PurchaseOrder::create([
            'supplier_id' => $this->supplier->id,
            'po_date' => now()->toDateString(),
            'status' => 'approved',
            'total_amount' => 15000.00,
        ]);

        Sanctum::actingAs($this->apiUser, ['*']);

        $payload = [
            'purchase_order_id' => $po->id,
            'received_date' => now()->toDateString(),
            'remarks' => 'Scanned via Handheld API scanner.',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity_received' => 100,
                    'batch_number' => 'BATCH-AB-123',
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/purchase/grn', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('goods_receipt_notes', [
            'purchase_order_id' => $po->id,
        ]);

        // PO status should update
        $this->assertEquals('received', $po->fresh()->status);
    }
}
