<?php

namespace Tests\Feature\Api;

use App\Models\Employee;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HRMApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $apiUser;

    protected Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->apiUser = User::factory()->create();

        $this->employee = Employee::create([
            'employee_id' => 'EMP-2026-9999',
            'first_name' => 'Alice',
            'last_name' => 'HRM Api',
            'email' => 'alice.hrm@liquorerp.in',
            'phone' => '9999900000',
            'department' => 'Finance',
            'designation' => 'Analyst',
            'joining_date' => '2026-02-01',
            'salary' => 45000.00,
            'status' => 'active',
        ]);
    }

    public function test_api_get_employee_roster(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $response = $this->getJson('/api/v1/hrm/roster');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => [['employee_id', 'first_name', 'email']]]);
    }

    public function test_api_biometric_clock_in_punch(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $payload = [
            'employee_id' => $this->employee->id,
            'check_in' => '09:10:00',
            'biometric_device_id' => 'B-PUNCH-009',
        ];

        $response = $this->postJson('/api/v1/hrm/punch', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['punch_id', 'status']]);
    }
}
