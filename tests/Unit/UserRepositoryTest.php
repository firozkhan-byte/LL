<?php

namespace Tests\Unit;

use App\Models\User;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository;

        // Seed default roles
        Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        Role::create(['name' => 'Cashier', 'guard_name' => 'web']);
    }

    /** @test */
    public function it_can_create_a_user()
    {
        $userData = [
            'name' => 'Alice Dev',
            'email' => 'alice@livingliquidz.com',
            'password' => bcrypt('Password123'),
            'status' => 'active',
        ];

        $user = $this->repository->create($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotEmpty($user->id); // UUID
        $this->assertEquals('Alice Dev', $user->name);
        $this->assertEquals('alice@livingliquidz.com', $user->email);
    }

    /** @test */
    public function it_can_update_a_user()
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $updatedUser = $this->repository->update($user->id, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updatedUser->name);
        $this->assertEquals('New Name', $user->fresh()->name);
    }

    /** @test */
    public function it_can_soft_delete_and_restore_a_user()
    {
        $user = User::factory()->create();

        $this->repository->delete($user->id);

        $this->assertSoftDeleted($user);
        $this->assertNull($this->repository->find($user->id));

        $this->repository->restore($user->id);

        $this->assertNotSoftDeleted($user);
        $this->assertNotNull($this->repository->find($user->id));
    }

    /** @test */
    public function it_filters_users_by_search_and_status()
    {
        User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@livingliquidz.com',
            'status' => 'active',
        ]);

        User::factory()->create([
            'name' => 'Bob Smith',
            'email' => 'bob@livingliquidz.com',
            'status' => 'suspended',
        ]);

        $activeUsers = $this->repository->getPaginated(['status' => 'active']);
        $this->assertCount(1, $activeUsers->items());
        $this->assertEquals('Jane Doe', $activeUsers->items()[0]->name);

        $searchUsers = $this->repository->getPaginated(['search' => 'bob']);
        $this->assertCount(1, $searchUsers->items());
        $this->assertEquals('Bob Smith', $searchUsers->items()[0]->name);
    }
}
