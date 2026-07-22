<?php

namespace Tests\Feature\Api;

use App\Models\ExciseLicense;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExciseApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $apiUser;

    protected ExciseLicense $license;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->apiUser = User::factory()->create();

        $this->license = ExciseLicense::create([
            'license_number' => 'LIC-EX-MH-9999',
            'license_type' => 'FL-III',
            'state' => 'Maharashtra',
            'expiry_date' => now()->addDays(45)->format('Y-m-d'),
            'status' => 'active',
            'renewal_fee' => 120000.00,
        ]);
    }

    public function test_api_get_compliance_gst_summary(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $response = $this->getJson('/api/v1/compliance/gst');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['gstr1_outward_supplies', 'net_gst_payable']]);
    }

    public function test_api_check_license_status(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $response = $this->getJson("/api/v1/compliance/license/{$this->license->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.license_number', 'LIC-EX-MH-9999')
            ->assertJsonPath('data.is_expiring_soon', false);
    }
}
