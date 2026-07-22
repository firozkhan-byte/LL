<?php

namespace Tests\Feature;

use App\Livewire\Admin\ApprovalsInbox;
use App\Livewire\Admin\CompanyManager;
use App\Models\Approval;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $managerUser;

    protected Company $company;

    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup permissions
        $manageCompanyPermission = Permission::create(['name' => 'manage-company', 'guard_name' => 'web']);
        $adminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($manageCompanyPermission);

        $managerRole = Role::create(['name' => 'Store Manager', 'guard_name' => 'web']);

        $this->adminUser = User::factory()->create(['status' => 'active']);
        $this->adminUser->assignRole($adminRole);

        $this->managerUser = User::factory()->create(['status' => 'active']);
        $this->managerUser->assignRole($managerRole);

        // Seed Company
        $this->company = Company::create([
            'name' => 'Living Liquidz Retail Ltd',
            'status' => 'active',
        ]);

        $this->branch = Branch::create([
            'company_id' => $this->company->id,
            'name' => 'Mumbai Main',
            'code' => 'BR-MUM-01',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function test_users_can_propose_store_creation_which_goes_to_approvals()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(CompanyManager::class)
            ->set('storeName', 'Colaba Store')
            ->set('storeCode', 'ST-COLABA')
            ->set('storeBranchId', $this->branch->id)
            ->set('storeLicense', 'EX-COL-999')
            ->call('proposeStore')
            ->assertHasNoErrors();

        // Check it is NOT created as an active store yet
        $this->assertDatabaseMissing('stores', ['code' => 'ST-COLABA']);

        // Check it exists in approvals table
        $this->assertDatabaseHas('approvals', [
            'approvable_type' => Store::class,
            'action' => 'create',
            'status' => 'pending',
            'requested_by' => $this->adminUser->id,
        ]);
    }

    /** @test */
    public function test_ceo_can_approve_proposed_changes_applying_them_to_database()
    {
        // Propose store first
        $approval = Approval::create([
            'approvable_type' => Store::class,
            'approvable_id' => null,
            'action' => 'create',
            'data' => [
                'branch_id' => $this->branch->id,
                'name' => 'Colaba Store',
                'code' => 'ST-COLABA',
                'license_number' => 'EX-COL-999',
            ],
            'status' => 'pending',
            'requested_by' => $this->adminUser->id,
        ]);

        $this->actingAs($this->adminUser);

        Livewire::test(ApprovalsInbox::class)
            ->call('openReviewModal', $approval->id)
            ->call('approveRequest')
            ->assertHasNoErrors();

        // Check approval is approved
        $this->assertEquals('approved', $approval->fresh()->status);
        $this->assertEquals($this->adminUser->id, $approval->fresh()->approved_by);

        // Check store is actually created and active
        $this->assertDatabaseHas('stores', [
            'code' => 'ST-COLABA',
            'name' => 'Colaba Store',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function test_ceo_can_reject_proposed_changes_with_reason()
    {
        // Propose store first
        $approval = Approval::create([
            'approvable_type' => Store::class,
            'approvable_id' => null,
            'action' => 'create',
            'data' => [
                'branch_id' => $this->branch->id,
                'name' => 'Colaba Store',
                'code' => 'ST-COLABA',
            ],
            'status' => 'pending',
            'requested_by' => $this->adminUser->id,
        ]);

        $this->actingAs($this->adminUser);

        Livewire::test(ApprovalsInbox::class)
            ->call('openReviewModal', $approval->id)
            ->set('rejectionReason', 'Incomplete branch license codes.')
            ->call('rejectRequest')
            ->assertHasNoErrors();

        // Check approval is rejected
        $this->assertEquals('rejected', $approval->fresh()->status);
        $this->assertEquals('Incomplete branch license codes.', $approval->fresh()->rejection_reason);

        // Check store is NOT created
        $this->assertDatabaseMissing('stores', ['code' => 'ST-COLABA']);
    }
}
