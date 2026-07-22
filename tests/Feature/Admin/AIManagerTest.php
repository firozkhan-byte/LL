<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\AIManager;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AIManagerTest extends TestCase
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

    public function test_ai_manager_component_can_be_rendered(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.ai'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(AIManager::class);
    }

    public function test_can_load_ceo_dashboard_and_chat_assistant(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(AIManager::class)
            ->set('activeTab', 'chat')
            ->set('chatInput', 'What are our monthly sales?')
            ->call('sendChatMessage')
            ->assertHasNoErrors()
            ->assertSee('What are our monthly sales?')
            ->assertSee('Simulated AI Business Analyst:');
    }
}
