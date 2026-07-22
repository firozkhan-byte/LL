<?php

namespace Tests\Feature\Api;

use App\Models\Account;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FinanceApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $apiUser;

    protected Account $cashAccount;

    protected Account $equityAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->apiUser = User::factory()->create();

        $this->cashAccount = Account::create([
            'code' => '1010',
            'name' => 'Cash Account',
            'type' => 'asset',
        ]);

        $this->equityAccount = Account::create([
            'code' => '3000',
            'name' => 'Owner Equity',
            'type' => 'equity',
        ]);
    }

    public function test_api_get_financial_statements(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $response = $this->getJson('/api/v1/finance/statements');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['profit_and_loss', 'balance_sheet', 'trial_balance']]);
    }

    public function test_api_post_external_balanced_journal(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $payload = [
            'entry_date' => '2026-07-16',
            'description' => 'API balanced journal',
            'lines' => [
                ['account_id' => $this->cashAccount->id, 'debit' => 5000.00, 'credit' => 0.00],
                ['account_id' => $this->equityAccount->id, 'debit' => 0.00, 'credit' => 5000.00],
            ],
        ];

        $response = $this->postJson('/api/v1/finance/journal', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['reference_number']]);
    }

    public function test_api_post_unbalanced_journal_fails(): void
    {
        Sanctum::actingAs($this->apiUser, ['*']);

        $payload = [
            'entry_date' => '2026-07-16',
            'description' => 'API unbalanced journal',
            'lines' => [
                ['account_id' => $this->cashAccount->id, 'debit' => 5000.00, 'credit' => 0.00],
                ['account_id' => $this->equityAccount->id, 'debit' => 0.00, 'credit' => 4500.00],
            ],
        ];

        $response = $this->postJson('/api/v1/finance/journal', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}
