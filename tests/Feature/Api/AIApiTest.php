<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AIApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $apiUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->apiUser = User::factory()->create();
    }

    public function test_api_get_sales_forecast(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $response = $this->getJson('/api/v1/ai/forecast');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'current_month_sales',
                    'projected_next_month_sales',
                    'growth_rate_projected',
                    'confidence_level_percentage',
                ],
            ]);
    }

    public function test_api_chat_query(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $payload = [
            'message' => 'predicted trend forecast',
        ];

        $response = $this->postJson('/api/v1/ai/chat', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['reply']);
    }
}
