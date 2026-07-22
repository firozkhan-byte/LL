<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\CRMManager;
use App\Models\CrmTicket;
use App\Models\Customer;
use App\Models\CustomerProfile;
use App\Models\CustomerWallet;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CRMManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Super Admin');

        $this->customer = Customer::create([
            'name' => 'Jane CRM Doe',
            'phone' => '9000000000',
            'membership_type' => 'regular',
            'loyalty_points' => 50,
        ]);

        CustomerProfile::create([
            'customer_id' => $this->customer->id,
            'birthday' => '1995-05-15',
            'anniversary' => '2020-10-10',
            'preferences' => ['preferred_category' => 'Wine'],
            'notes' => 'Test notes',
        ]);

        CustomerWallet::create([
            'customer_id' => $this->customer->id,
            'balance' => 1000.00,
        ]);
    }

    public function test_crm_manager_component_can_be_rendered(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.crm'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(CRMManager::class);
    }

    public function test_can_view_profile_and_adjust_wallet_balance(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(CRMManager::class)
            ->call('viewCustomerProfile', $this->customer->id)
            ->assertSet('currentWalletBalance', 1000.00)
            ->set('walletAdjustType', 'deposit')
            ->set('walletAmount', 500.00)
            ->call('adjustWalletBalance')
            ->assertSet('currentWalletBalance', 1500.00)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('customer_wallets', [
            'customer_id' => $this->customer->id,
            'balance' => 1500.00,
        ]);
    }

    public function test_wallet_withdrawal_validation(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(CRMManager::class)
            ->call('viewCustomerProfile', $this->customer->id)
            ->set('walletAdjustType', 'withdrawal')
            ->set('walletAmount', 2000.00)
            ->call('adjustWalletBalance')
            ->assertHasErrors(['walletAmount']);
    }

    public function test_can_register_and_resolve_ticket(): void
    {
        // 1. Create a support ticket via livewire component
        Livewire::actingAs($this->adminUser)
            ->test(CRMManager::class)
            ->set('ticketCustomerId', $this->customer->id)
            ->set('ticketTypeField', 'complaint')
            ->set('ticketSubject', 'Refund Issue')
            ->set('ticketDescription', 'I was charged twice.')
            ->set('ticketPriority', 'high')
            ->call('saveTicket')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('crm_tickets', [
            'customer_id' => $this->customer->id,
            'subject' => 'Refund Issue',
            'status' => 'open',
        ]);

        $ticket = CrmTicket::first();

        // 2. Resolve the ticket
        Livewire::actingAs($this->adminUser)
            ->test(CRMManager::class)
            ->call('openTicketModal', $ticket->id)
            ->set('newTicketStatus', 'resolved')
            ->call('updateTicketStatus')
            ->assertHasNoErrors();

        $this->assertEquals('resolved', $ticket->fresh()->status);
    }

    public function test_can_dispatch_marketing_campaign(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(CRMManager::class)
            ->set('campaignName', 'Test SMS Promo')
            ->set('campaignChannel', 'sms')
            ->set('campaignContent', 'This is a test marketing text message.')
            ->set('campaignRecipientsCount', 50)
            ->call('sendCampaign')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('crm_campaigns', [
            'name' => 'Test SMS Promo',
            'channel' => 'sms',
            'status' => 'sent',
            'sent_count' => 50,
        ]);
    }
}
