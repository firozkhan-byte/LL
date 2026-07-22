<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\ReportManager;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportManagerTest extends TestCase
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

    public function test_report_manager_component_can_be_rendered(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.reports'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(ReportManager::class);
    }

    public function test_can_load_operational_summaries_and_trigger_csv_download(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(ReportManager::class)
            ->set('startDate', '2026-07-01')
            ->set('endDate', '2026-07-31')
            ->call('exportCSV')
            ->assertHasNoErrors()
            ->assertSee('Sales Analytics');
    }
}
