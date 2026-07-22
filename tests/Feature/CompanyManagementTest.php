<?php

namespace Tests\Feature;

use App\Livewire\Admin\CompanyManager;
use App\Livewire\Admin\CompanyTree;
use App\Models\Branch;
use App\Models\Company;
use App\Models\RegionalOffice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CompanyManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Company $company;

    protected RegionalOffice $regionalOffice;

    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup permissions
        $manageCompanyPermission = Permission::create(['name' => 'manage-company', 'guard_name' => 'web']);
        $adminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($manageCompanyPermission);

        Role::create(['name' => 'Store Manager', 'guard_name' => 'web']);

        $this->adminUser = User::factory()->create(['status' => 'active']);
        $this->adminUser->assignRole($adminRole);

        // Seed Company
        $this->company = Company::create([
            'name' => 'Living Liquidz Retail Ltd',
            'registration_number' => 'U51228MH2002PLC137943',
            'status' => 'active',
        ]);

        $this->company->settings()->create([
            'currency' => 'INR',
            'timezone' => 'Asia/Kolkata',
        ]);

        $this->regionalOffice = RegionalOffice::create([
            'company_id' => $this->company->id,
            'name' => 'Western region',
            'code' => 'RO-WEST',
        ]);

        $this->branch = Branch::create([
            'company_id' => $this->company->id,
            'regional_office_id' => $this->regionalOffice->id,
            'name' => 'Mumbai Main',
            'code' => 'BR-MUM-01',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function test_guests_cannot_access_company_tree()
    {
        $this->get(route('admin.company-tree'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function test_unauthorized_users_cannot_access_company_tree()
    {
        $normalUser = User::factory()->create(['status' => 'active']);

        $this->actingAs($normalUser)
            ->get(route('admin.company-tree'))
            ->assertStatus(403);
    }

    /** @test */
    public function test_authorized_admin_can_view_company_tree()
    {
        $this->actingAs($this->adminUser)
            ->get(route('admin.company-tree'))
            ->assertStatus(200)
            ->assertSeeLivewire(CompanyTree::class)
            ->assertSee('Living Liquidz Retail Ltd')
            ->assertSee('Mumbai Main');
    }

    /** @test */
    public function test_admin_can_edit_company_settings()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(CompanyManager::class)
            ->assertSet('companyName', 'Living Liquidz Retail Ltd')
            ->set('companyName', 'Living Liquidz Pvt Ltd')
            ->set('city', 'Mumbai')
            ->call('updateCompany')
            ->assertHasNoErrors();

        $this->assertEquals('Living Liquidz Pvt Ltd', $this->company->fresh()->name);
        $this->assertEquals('Mumbai', $this->company->fresh()->settings->city);
    }

    /** @test */
    public function test_admin_can_create_metadata_structures_immediately()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(CompanyManager::class)
            ->set('structureType', 'department')
            ->set('structName', 'Excise Compliance')
            ->set('structCode', 'DEPT-EXCISE')
            ->call('addStructure')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('departments', [
            'name' => 'Excise Compliance',
            'code' => 'DEPT-EXCISE',
        ]);
    }
}
