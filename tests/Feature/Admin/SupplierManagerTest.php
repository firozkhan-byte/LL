<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\SupplierManager;
use App\Models\Supplier;
use App\Models\User;
use App\Services\ApprovalService;
use App\Services\SupplierService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SupplierManagerTest extends TestCase
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

    public function test_supplier_manager_component_can_be_rendered(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.suppliers'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(SupplierManager::class);
    }

    public function test_supplier_onboarding_proposes_approval(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(SupplierManager::class)
            ->set('name', 'United Spirits Limited')
            ->set('gstin', '27AAACU1234A1Z1')
            ->set('pan', 'AAACU1234A')
            ->set('paymentTermsDays', 45)
            ->set('creditLimit', 100000)
            ->set('rating', 4.5)
            ->set('contactsList', [
                [
                    'name' => 'Amit Sharma',
                    'email' => 'amit@diageo.com',
                    'phone' => '9876543210',
                    'designation' => 'Billing Desk',
                    'is_primary' => true,
                ],
            ])
            ->call('saveSupplier')
            ->assertHasNoErrors();

        // The supplier shouldn't exist in final active state, but should be registered in approvals table as a proposal
        $this->assertDatabaseHas('approvals', [
            'approvable_type' => Supplier::class,
            'action' => 'create',
            'status' => 'pending',
        ]);
    }

    public function test_approving_supplier_onboarding_promotes_status_to_active(): void
    {
        // 1. Propose change
        $payload = [
            'name' => 'Radico Khaitan',
            'gstin' => '27AAACR4321D4Z4',
            'pan' => 'AAACR4321D',
            'payment_terms_days' => 30,
            'credit_limit' => 50000,
            'rating' => 4.0,
            'status' => 'pending_approval',
            'contacts' => [
                [
                    'name' => 'Sanjay Dutt',
                    'email' => 'sanjay@radico.com',
                    'phone' => '9876543211',
                    'designation' => 'Onboarding Lead',
                    'is_primary' => true,
                ],
            ],
        ];

        $approval = resolve(SupplierService::class)->proposeSupplier($payload, $this->adminUser->id);

        $this->assertDatabaseHas('approvals', [
            'id' => $approval->id,
            'status' => 'pending',
        ]);

        // 2. Approve request through ApprovalService
        resolve(ApprovalService::class)->approve($approval->id, $this->adminUser->id);

        // 3. Confirm supplier is created with status active
        $this->assertDatabaseHas('suppliers', [
            'name' => 'Radico Khaitan',
            'status' => 'active',
        ]);
    }

    public function test_supplier_list_search_and_filters(): void
    {
        $s1 = Supplier::create([
            'name' => 'Diageo Premium',
            'code' => 'SUP-DIAGEO',
            'rating' => 4.8,
            'payment_terms_days' => 45,
            'status' => 'active',
        ]);

        $s2 = Supplier::create([
            'name' => 'Kingfisher Breweries',
            'code' => 'SUP-KF',
            'rating' => 3.5,
            'payment_terms_days' => 15,
            'status' => 'active',
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(SupplierManager::class)
            ->set('search', 'Diageo')
            ->assertSee('Diageo Premium')
            ->assertDontSee('Kingfisher Breweries');

        Livewire::actingAs($this->adminUser)
            ->test(SupplierManager::class)
            ->set('selectedRating', 4.0)
            ->assertSee('Diageo Premium')
            ->assertDontSee('Kingfisher Breweries');
    }
}
