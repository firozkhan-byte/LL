<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\CustomerProfile;
use App\Models\CustomerWallet;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CRMApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $apiUser;

    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->apiUser = User::factory()->create();

        $this->customer = Customer::create([
            'name' => 'Alice CRM Api',
            'phone' => '9999000000',
            'membership_type' => 'vip',
            'loyalty_points' => 300,
        ]);

        CustomerProfile::create([
            'customer_id' => $this->customer->id,
            'birthday' => '1990-12-25',
            'anniversary' => '2015-06-20',
            'preferences' => ['preferred_category' => 'Whisky'],
        ]);

        CustomerWallet::create([
            'customer_id' => $this->customer->id,
            'balance' => 4500.00,
        ]);
    }

    public function test_api_get_customer_crm_profile(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $response = $this->getJson("/api/v1/crm/customers/{$this->customer->phone}");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Alice CRM Api')
            ->assertJsonPath('data.membership_type', 'vip')
            ->assertJsonPath('data.wallet_balance', 4500);
    }

    public function test_api_create_support_ticket(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $payload = [
            'customer_id' => $this->customer->id,
            'type' => 'support',
            'subject' => 'Loyalty conversion',
            'description' => 'I cannot convert my points to wallet money.',
            'priority' => 'medium',
        ];

        $response = $this->postJson('/api/v1/crm/tickets', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['ticket_id', 'status']]);
    }
}
