<?php

namespace Tests\Feature;

use App\Livewire\Admin\UserManagement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup roles & permissions
        $manageUsersPermission = Permission::create(['name' => 'manage-users', 'guard_name' => 'web']);
        $adminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($manageUsersPermission);

        Role::create(['name' => 'Cashier', 'guard_name' => 'web']);

        // Create Admin
        $this->adminUser = User::factory()->create([
            'email' => 'admin@livingliquidz.com',
            'status' => 'active',
        ]);
        $this->adminUser->assignRole($adminRole);
    }

    /** @test */
    public function guests_cannot_access_user_management()
    {
        $this->get(route('admin.users'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function unauthorized_users_cannot_access_user_management()
    {
        $normalUser = User::factory()->create(['status' => 'active']);

        $this->actingAs($normalUser)
            ->get(route('admin.users'))
            ->assertStatus(403);
    }

    /** @test */
    public function admin_can_see_user_management_component()
    {
        $this->actingAs($this->adminUser)
            ->get(route('admin.users'))
            ->assertStatus(200)
            ->assertSeeLivewire(UserManagement::class);
    }

    /** @test */
    public function admin_can_create_user_via_livewire()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(UserManagement::class)
            ->call('openCreateModal')
            ->set('name', 'Developer Test')
            ->set('email', 'devtest@livingliquidz.com')
            ->set('password', 'Password123')
            ->set('selectedRoles', ['Cashier'])
            ->set('status', 'active')
            ->call('saveUser')
            ->assertHasNoErrors()
            ->assertSet('showingUserModal', false);

        $this->assertDatabaseHas('users', [
            'email' => 'devtest@livingliquidz.com',
            'name' => 'Developer Test',
            'status' => 'active',
        ]);

        $newUser = User::where('email', 'devtest@livingliquidz.com')->first();
        $this->assertTrue($newUser->hasRole('Cashier'));
    }

    /** @test */
    public function admin_can_edit_and_update_user_status()
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('Cashier');

        $this->actingAs($this->adminUser);

        Livewire::test(UserManagement::class)
            ->call('openEditModal', $user->id)
            ->assertSet('name', $user->name)
            ->set('status', 'suspended')
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertEquals('suspended', $user->fresh()->status);
    }

    /** @test */
    public function admin_can_soft_delete_user()
    {
        $user = User::factory()->create();

        $this->actingAs($this->adminUser);

        Livewire::test(UserManagement::class)
            ->call('deleteUser', $user->id);

        $this->assertSoftDeleted($user);
    }
}
