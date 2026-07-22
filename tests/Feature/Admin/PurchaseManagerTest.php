<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\PurchaseManager;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PurchaseManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Product $product;

    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Super Admin');

        $this->product = Product::create([
            'name' => 'Johnnie Walker Black Label',
            'volume_ml' => 750,
            'alcohol_percentage' => 42.80,
            'mrp' => 4500.00,
            'purchase_price' => 3500.00,
            'selling_price' => 4500.00,
            'liquor_type' => 'Spirit',
            'status' => 'active',
        ]);

        $this->supplier = Supplier::create([
            'name' => 'United Spirits Ltd',
            'code' => 'SUP-USL-TEST',
            'outstanding_balance' => 0.00,
            'status' => 'active',
        ]);
    }

    public function test_purchase_manager_component_can_be_rendered(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.purchase'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(PurchaseManager::class);
    }

    public function test_creating_purchase_requisition_success(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(PurchaseManager::class)
            ->set('reqNeededDate', now()->addDays(5)->toDateString())
            ->set('reqItems', [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 150,
                    'estimated_cost' => 3500.00,
                ],
            ])
            ->call('saveRequisition')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('purchase_requisitions', [
            'requested_by' => $this->adminUser->id,
            'status' => 'approved',
        ]);
    }

    public function test_generating_purchase_order_success(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(PurchaseManager::class)
            ->set('poSupplierId', $this->supplier->id)
            ->set('poDate', now()->toDateString())
            ->set('poItems', [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 50,
                    'unit_price' => 3200.00,
                    'tax_percent' => 18.00,
                ],
            ])
            ->call('savePurchaseOrder')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('purchase_orders', [
            'supplier_id' => $this->supplier->id,
            'status' => 'approved',
            'total_amount' => 188800.00, // (3200 * 50) + 18% GST
        ]);
    }

    public function test_logging_goods_receipt_note_success(): void
    {
        $po = PurchaseOrder::create([
            'supplier_id' => $this->supplier->id,
            'po_date' => now()->toDateString(),
            'status' => 'approved',
            'total_amount' => 10000.00,
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(PurchaseManager::class)
            ->set('grnPoId', $po->id)
            ->set('grnReceivedDate', now()->toDateString())
            ->set('grnItems', [
                [
                    'product_id' => $this->product->id,
                    'quantity_ordered' => 10,
                    'quantity_received' => 10,
                    'quantity_accepted' => 10,
                    'quantity_rejected' => 0,
                    'batch_number' => 'BATCH-123',
                    'expiry_date' => '',
                ],
            ])
            ->call('saveGRN')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('goods_receipt_notes', [
            'purchase_order_id' => $po->id,
            'status' => 'completed',
        ]);

        // PO status should transition to received
        $this->assertEquals('received', $po->fresh()->status);
    }

    public function test_invoice_recording_updates_supplier_outstanding_balance(): void
    {
        $this->assertEquals(0.00, $this->supplier->outstanding_balance);

        Livewire::actingAs($this->adminUser)
            ->test(PurchaseManager::class)
            ->set('invSupplierId', $this->supplier->id)
            ->set('invNumber', 'INV-USL-9988')
            ->set('invDate', now()->toDateString())
            ->set('invDueDate', now()->addDays(30)->toDateString())
            ->set('invItems', [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 10,
                    'unit_price' => 3000.00,
                ],
            ])
            ->call('saveInvoice')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('purchase_invoices', [
            'supplier_id' => $this->supplier->id,
            'invoice_number' => 'INV-USL-9988',
        ]);

        // Supplier outstanding balance should be updated by total invoice cost (3000 * 10) + 18% GST = 35400.00
        $this->assertEquals(35400.00, $this->supplier->fresh()->outstanding_balance);
    }
}
