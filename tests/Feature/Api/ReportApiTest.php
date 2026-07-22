<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReportApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $apiUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->apiUser = User::factory()->create();
    }

    public function test_api_get_summary_report(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $response = $this->getJson('/api/v1/reports/summary?start_date=2026-07-01&end_date=2026-07-31');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'sales' => ['count', 'total', 'aov'],
                    'purchase' => ['count', 'total'],
                    'inventory' => ['total_items', 'valuation', 'products_count'],
                    'finance' => ['debits', 'credits'],
                    'compliance' => ['output_gst', 'input_gst', 'net_gst', 'active_licenses'],
                    'customers' => ['total', 'new'],
                    'suppliers' => ['total'],
                    'warehouses' => ['total'],
                    'hr' => ['headcount', 'attendance_rate'],
                    'branches' => ['total'],
                ],
            ]);
    }
}
