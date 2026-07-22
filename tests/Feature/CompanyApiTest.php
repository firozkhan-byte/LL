<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\RegionalOffice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CompanyApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected string $adminToken;

    protected Company $company;

    protected RegionalOffice $regionalOffice;

    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles and permissions
        $manageCompanyPermission = Permission::create(['name' => 'manage-company', 'guard_name' => 'web']);
        $adminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($manageCompanyPermission);

        // Create Admin user and token
        $this->adminUser = User::factory()->create([
            'email' => 'admin@livingliquidz.com',
            'status' => 'active',
        ]);
        $this->adminUser->assignRole($adminRole);
        $this->adminToken = $this->adminUser->createToken('test')->plainTextToken;

        // Create Company & structures
        $this->company = Company::create([
            'name' => 'Living Liquidz Retail Ltd',
            'status' => 'active',
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
    public function test_authorized_admin_can_fetch_company_list_via_api()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->adminToken,
        ])->getJson('/api/v1/companies');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.name', 'Living Liquidz Retail Ltd');
    }

    /** @test */
    public function test_authorized_admin_can_fetch_company_tree_via_api()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->adminToken,
        ])->getJson('/api/v1/companies/'.$this->company->id.'/tree');

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Living Liquidz Retail Ltd')
            ->assertJsonPath('data.regional_offices.0.name', 'Western region')
            ->assertJsonPath('data.regional_offices.0.branches.0.name', 'Mumbai Main');
    }
}
