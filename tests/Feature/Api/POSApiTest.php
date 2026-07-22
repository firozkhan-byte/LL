<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\GiftCard;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class POSApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $apiUser;

    protected Customer $customer;

    protected GiftCard $giftCard;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->apiUser = User::factory()->create();

        $this->customer = Customer::create([
            'name' => 'Rajesh Kumar',
            'phone' => '9888888888',
            'membership_type' => 'gold',
            'loyalty_points' => 340,
        ]);

        $this->giftCard = GiftCard::create([
            'card_number' => 'GC-API-111',
            'balance' => 4500,
            'is_active' => true,
        ]);
    }

    public function test_api_customer_lookup_endpoint(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $response = $this->getJson("/api/v1/pos/customers/{$this->customer->phone}");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Rajesh Kumar');
    }

    public function test_api_gift_card_balance_endpoint(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $response = $this->getJson("/api/v1/pos/giftcards/{$this->giftCard->card_number}");

        $response->assertStatus(200)
            ->assertJsonPath('data.balance', 4500);
    }
}
