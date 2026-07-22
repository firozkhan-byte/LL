<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected string $adminToken;

    protected User $cashierUser;

    protected string $cashierToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles and permissions
        $manageUsersPermission = Permission::create(['name' => 'manage-users', 'guard_name' => 'web']);
        $adminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($manageUsersPermission);

        $cashierRole = Role::create(['name' => 'Cashier', 'guard_name' => 'web']);

        // Create Admin user and token
        $this->adminUser = User::factory()->create([
            'email' => 'admin@livingliquidz.com',
            'password' => bcrypt('Password123'),
            'status' => 'active',
        ]);
        $this->adminUser->assignRole($adminRole);
        $this->adminToken = $this->adminUser->createToken('test')->plainTextToken;

        // Create Cashier user and token
        $this->cashierUser = User::factory()->create([
            'email' => 'cashier@livingliquidz.com',
            'password' => bcrypt('Password123'),
            'status' => 'active',
        ]);
        $this->cashierUser->assignRole($cashierRole);
        $this->cashierToken = $this->cashierUser->createToken('test')->plainTextToken;
    }

    /** @test */
    public function guests_can_login_via_api()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@livingliquidz.com',
            'password' => 'Password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user']);
    }

    /** @test */
    public function suspended_users_cannot_login_via_api()
    {
        $suspendedUser = User::factory()->create([
            'email' => 'suspended@livingliquidz.com',
            'password' => bcrypt('Password123'),
            'status' => 'suspended',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'suspended@livingliquidz.com',
            'password' => 'Password123',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function authenticated_user_can_fetch_self_profile()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->cashierToken,
        ])->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('data.email', 'cashier@livingliquidz.com')
            ->assertJsonPath('data.roles.0', 'Cashier');
    }

    /** @test */
    public function authorized_admin_can_list_users()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->adminToken,
        ])->getJson('/api/v1/users');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data'); // Admin + Cashier
    }

    /** @test */
    public function unauthorized_cashier_cannot_list_users()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->cashierToken,
        ])->getJson('/api/v1/users');

        $response->assertStatus(403);
    }

    /** @test */
    public function authorized_admin_can_create_user_via_api()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->adminToken,
        ])->postJson('/api/v1/users', [
            'name' => 'API Created User',
            'email' => 'apicreated@livingliquidz.com',
            'password' => 'Password123',
            'status' => 'active',
            'roles' => ['Cashier'],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'apicreated@livingliquidz.com']);
    }

    /** @test */
    public function authorized_admin_can_delete_user_via_api()
    {
        $userToDelete = User::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->adminToken,
        ])->deleteJson('/api/v1/users/'.$userToDelete->id);

        $response->assertStatus(200);
        $this->assertSoftDeleted($userToDelete);
    }
}
