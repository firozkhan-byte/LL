<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\FinanceManager;
use App\Models\Account;
use App\Models\DepreciationSchedule;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinanceManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Account $cashAccount;

    protected Account $equityAccount;

    protected Account $expenseAccount;

    protected DepreciationSchedule $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Super Admin');

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

        $this->expenseAccount = Account::create([
            'code' => '6100',
            'name' => 'Utility Expense',
            'type' => 'expense',
        ]);

        $this->asset = DepreciationSchedule::create([
            'asset_name' => 'Depot Truck',
            'purchase_cost' => 500000.00,
            'salvage_value' => 50000.00,
            'useful_life_years' => 5,
            'current_value' => 500000.00,
        ]);
    }

    public function test_finance_manager_component_can_be_rendered(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.finance'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(FinanceManager::class);
    }

    public function test_can_create_new_gl_account(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(FinanceManager::class)
            ->call('openAccountModal')
            ->set('newAccountCode', '1020')
            ->set('newAccountName', 'Bank Account')
            ->set('newAccountType', 'asset')
            ->call('saveAccount')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('accounts', [
            'code' => '1020',
            'name' => 'Bank Account',
        ]);
    }

    public function test_can_post_balanced_journal_entry(): void
    {
        // Debit Cash ₹10,000, Credit Equity ₹10,000
        Livewire::actingAs($this->adminUser)
            ->test(FinanceManager::class)
            ->call('openJournalModal')
            ->set('journalEntryDate', '2026-07-16')
            ->set('journalDescription', 'Test posting')
            ->set('journalLines', [
                ['account_id' => $this->cashAccount->id, 'debit' => 10000.00, 'credit' => 0.00],
                ['account_id' => $this->equityAccount->id, 'debit' => 0.00, 'credit' => 10000.00],
            ])
            ->call('saveJournal')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('journal_entries', [
            'description' => 'Test posting',
            'status' => 'posted',
        ]);
    }

    public function test_journal_posting_fails_if_unbalanced(): void
    {
        // Debit Cash ₹10,000, Credit Equity ₹8,000 (Out of balance by ₹2,000)
        Livewire::actingAs($this->adminUser)
            ->test(FinanceManager::class)
            ->call('openJournalModal')
            ->set('journalEntryDate', '2026-07-16')
            ->set('journalDescription', 'Unbalanced test')
            ->set('journalLines', [
                ['account_id' => $this->cashAccount->id, 'debit' => 10000.00, 'credit' => 0.00],
                ['account_id' => $this->equityAccount->id, 'debit' => 0.00, 'credit' => 8000.00],
            ])
            ->call('saveJournal')
            ->assertHasErrors(['journalLines']);
    }

    public function test_depreciation_scheduler_calculation(): void
    {
        // SL Depreciation on 500k cost, 50k salvage, 5 years = (500k - 50k)/5 = 90k annual depreciation
        // Current value: 500k -> 410k after 1 depreciation cycle
        Livewire::actingAs($this->adminUser)
            ->test(FinanceManager::class)
            ->call('depreciateAsset', $this->asset->id)
            ->assertHasNoErrors();

        $this->assertEquals(410000.00, $this->asset->fresh()->current_value);
    }
}
