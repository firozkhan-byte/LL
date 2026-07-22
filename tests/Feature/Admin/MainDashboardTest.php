<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\MainDashboard;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MainDashboardTest extends TestCase
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

    public function test_main_dashboard_route_can_be_rendered(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(MainDashboard::class);
    }

    public function test_can_query_chatbot_assistant_on_main_dashboard(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(MainDashboard::class)
            ->set('chatInput', 'Show total sales')
            ->call('askAI')
            ->assertHasNoErrors()
            ->assertSee('Show total sales')
            ->assertSee('Simulated AI Business Analyst:');
    }
}
