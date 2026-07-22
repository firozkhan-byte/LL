<?php

namespace App\Livewire\Admin;

use App\Models\CrmCampaign;
use App\Models\CrmTicket;
use App\Models\Customer;
use App\Models\CustomerProfile;
use App\Models\User;
use App\Services\CRMService;
use Livewire\Component;
use Livewire\WithPagination;

class CRMManager extends Component
{
    use WithPagination;

    public string $activeTab = 'database';

    // Filters
    public string $search = '';

    public string $ticketStatus = '';

    public string $ticketType = '';

    // Selected Customer Details Modal
    public bool $showingProfileModal = false;

    public ?Customer $selectedCustomer = null;

    public ?CustomerProfile $selectedProfile = null;

    public float $currentWalletBalance = 0.00;

    // Wallet Adjustments Form
    public float $walletAmount = 0.00;

    public string $walletAdjustType = 'deposit'; // deposit, withdrawal

    // Support Ticket Resolver
    public bool $showingTicketModal = false;

    public ?CrmTicket $selectedTicket = null;

    public string $newTicketStatus = 'open';

    // Create Support Ticket Form
    public bool $showingCreateTicketModal = false;

    public ?string $ticketCustomerId = null;

    public string $ticketTypeField = 'support';

    public string $ticketSubject = '';

    public string $ticketDescription = '';

    public string $ticketPriority = 'medium';

    public string $customerSearch = '';

    // Campaign Dispatch Form
    public string $campaignName = '';

    public string $campaignChannel = 'email';

    public string $campaignSubject = '';

    public string $campaignContent = '';

    public int $campaignRecipientsCount = 100;

    protected $queryString = [
        'search' => ['except' => ''],
        'ticketStatus' => ['except' => ''],
        'ticketType' => ['except' => ''],
        'activeTab' => ['except' => 'database'],
    ];

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // --- Profile & Wallet Details ---
    public function viewCustomerProfile(string $customerId, CRMService $crmService): void
    {
        $this->selectedCustomer = Customer::with('profile', 'wallet')->find($customerId);
        if ($this->selectedCustomer) {
            $this->selectedProfile = $this->selectedCustomer->profile;
            $this->currentWalletBalance = $crmService->adjustWallet($customerId, 0, 'deposit')->balance; // get current balance safely
            $this->walletAmount = 0.00;
            $this->walletAdjustType = 'deposit';
            $this->showingProfileModal = true;
        }
    }

    public function adjustWalletBalance(CRMService $crmService): void
    {
        $this->validate([
            'walletAmount' => 'required|numeric|min:0.01',
            'walletAdjustType' => 'required|in:deposit,withdrawal',
        ]);

        if ($this->walletAdjustType === 'withdrawal' && $this->walletAmount > $this->currentWalletBalance) {
            $this->addError('walletAmount', 'Withdrawal amount cannot exceed available wallet balance.');

            return;
        }

        $crmService->adjustWallet(
            $this->selectedCustomer->id,
            $this->walletAmount,
            $this->walletAdjustType,
            'manual_adjustment'
        );

        $this->currentWalletBalance = $crmService->adjustWallet($this->selectedCustomer->id, 0, 'deposit')->balance;
        $this->walletAmount = 0.00;

        session()->flash('wallet_success', 'Wallet balance adjusted successfully.');
    }

    // --- Support Tickets Resolutions ---
    public function openTicketModal(string $ticketId): void
    {
        $this->selectedTicket = CrmTicket::with('customer')->find($ticketId);
        if ($this->selectedTicket) {
            $this->newTicketStatus = $this->selectedTicket->status;
            $this->showingTicketModal = true;
        }
    }

    public function updateTicketStatus(CRMService $crmService): void
    {
        if ($this->selectedTicket) {
            if ($this->newTicketStatus === 'resolved') {
                $crmService->resolveTicket($this->selectedTicket->id);
            } else {
                $this->selectedTicket->update(['status' => $this->newTicketStatus]);
            }
            session()->flash('ticket_success', 'Support ticket status updated.');
            $this->showingTicketModal = false;
        }
    }

    // --- Create Support Ticket ---
    public function openCreateTicketModal(): void
    {
        $this->ticketCustomerId = null;
        $this->ticketSubject = '';
        $this->ticketDescription = '';
        $this->ticketPriority = 'medium';
        $this->customerSearch = '';
        $this->showingCreateTicketModal = true;
    }

    public function saveTicket(CRMService $crmService): void
    {
        $this->validate([
            'ticketCustomerId' => 'required|exists:customers,id',
            'ticketSubject' => 'required|string|max:255',
            'ticketDescription' => 'required|string',
            'ticketPriority' => 'required|in:low,medium,high',
            'ticketTypeField' => 'required|in:feedback,complaint,support',
        ]);

        $crmService->createTicket([
            'customer_id' => $this->ticketCustomerId,
            'type' => $this->ticketTypeField,
            'subject' => $this->ticketSubject,
            'description' => $this->ticketDescription,
            'status' => 'open',
            'priority' => $this->ticketPriority,
            'assigned_to' => auth()->id() ?? User::first()?->id,
        ]);

        session()->flash('success', 'Support ticket registered successfully.');
        $this->showingCreateTicketModal = false;
    }

    // --- Campaign Dispatches ---
    public function sendCampaign(CRMService $crmService): void
    {
        $this->validate([
            'campaignName' => 'required|string|max:255',
            'campaignChannel' => 'required|in:email,sms,whatsapp',
            'campaignContent' => 'required|string',
        ]);

        $crmService->dispatchCampaign([
            'name' => $this->campaignName,
            'channel' => $this->campaignChannel,
            'subject' => $this->campaignChannel === 'email' ? $this->campaignSubject : null,
            'content' => $this->campaignContent,
            'sent_count' => $this->campaignRecipientsCount,
        ]);

        session()->flash('success', 'Marketing campaign sent to '.$this->campaignRecipientsCount.' loyalty members.');

        $this->campaignName = '';
        $this->campaignSubject = '';
        $this->campaignContent = '';
    }

    public function render(CRMService $crmService)
    {
        $customersList = [];
        $ticketsList = [];
        $campaignsList = [];
        $analytics = [];

        if ($this->activeTab === 'database') {
            $customersList = Customer::with('profile')
                ->when($this->search, function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
                })
                ->orderBy('name')
                ->paginate(10);
        } elseif ($this->activeTab === 'tickets') {
            $filters = [
                'status' => $this->ticketStatus,
                'type' => $this->ticketType,
                'search' => $this->search,
            ];
            $ticketsList = $crmService->getTickets($filters, 10);
        } elseif ($this->activeTab === 'campaigns') {
            $campaignsList = CrmCampaign::orderBy('created_at', 'desc')->paginate(10);
        } elseif ($this->activeTab === 'analytics') {
            $analytics = $crmService->getCRMAnalytics();
        }

        // Dynamic search for creating tickets
        $customerSuggestions = Customer::when($this->customerSearch, function ($q) {
            $q->where('name', 'like', "%{$this->customerSearch}%")
                ->orWhere('phone', 'like', "%{$this->customerSearch}%");
        })->take(5)->get();

        return view('livewire.admin.c-r-m-manager', [
            'customersList' => $customersList,
            'ticketsList' => $ticketsList,
            'campaignsList' => $campaignsList,
            'analytics' => $analytics,
            'customerSuggestions' => $customerSuggestions,
        ])->layout('layouts.app');
    }
}
