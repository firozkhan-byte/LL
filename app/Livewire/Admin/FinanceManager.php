<?php

namespace App\Livewire\Admin;

use App\Models\Account;
use App\Models\Budget;
use App\Models\DepreciationSchedule;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Services\FinanceService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class FinanceManager extends Component
{
    use WithPagination;

    public string $activeTab = 'accounts';

    // Filters
    public string $search = '';

    // Create Account Form
    public bool $showingAccountModal = false;

    public string $newAccountCode = '';

    public string $newAccountName = '';

    public string $newAccountType = 'asset'; // asset, liability, equity, revenue, expense

    // Create Journal Entry Form
    public bool $showingJournalModal = false;

    public string $journalEntryDate = '';

    public string $journalDescription = '';

    public array $journalLines = []; // [['account_id' => '', 'debit' => 0.00, 'credit' => 0.00]]

    // Create Budget Form
    public bool $showingBudgetModal = false;

    public ?string $budgetAccountId = null;

    public int $budgetYear = 2026;

    public float $budgetAllocated = 0.00;

    protected $queryString = [
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'accounts'],
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

    // --- Chart of Accounts Operations ---
    public function openAccountModal(): void
    {
        $this->newAccountCode = '';
        $this->newAccountName = '';
        $this->newAccountType = 'asset';
        $this->showingAccountModal = true;
    }

    public function saveAccount(FinanceService $financeService): void
    {
        $this->validate([
            'newAccountCode' => 'required|string|unique:accounts,code',
            'newAccountName' => 'required|string|max:255',
            'newAccountType' => 'required|in:asset,liability,equity,revenue,expense',
        ]);

        $financeService->createAccount([
            'code' => $this->newAccountCode,
            'name' => $this->newAccountName,
            'type' => $this->newAccountType,
            'status' => 'active',
        ]);

        session()->flash('account_success', 'Account added to Chart of Accounts.');
        $this->showingAccountModal = false;
    }

    // --- Journal Entry Operations ---
    public function openJournalModal(): void
    {
        $this->journalEntryDate = date('Y-m-d');
        $this->journalDescription = '';
        $this->journalLines = [
            ['account_id' => Account::first()?->id, 'debit' => 0.00, 'credit' => 0.00],
            ['account_id' => Account::first()?->id, 'debit' => 0.00, 'credit' => 0.00],
        ];
        $this->showingJournalModal = true;
    }

    public function addJournalLine(): void
    {
        $this->journalLines[] = [
            'account_id' => Account::first()?->id,
            'debit' => 0.00,
            'credit' => 0.00,
        ];
    }

    public function removeJournalLine(int $index): void
    {
        unset($this->journalLines[$index]);
        $this->journalLines = array_values($this->journalLines);
    }

    public function saveJournal(FinanceService $financeService): void
    {
        $this->validate([
            'journalEntryDate' => 'required|date',
            'journalDescription' => 'required|string',
            'journalLines' => 'required|array|min:2',
            'journalLines.*.account_id' => 'required|exists:accounts,id',
            'journalLines.*.debit' => 'required|numeric|min:0',
            'journalLines.*.credit' => 'required|numeric|min:0',
        ]);

        try {
            $financeService->postJournal([
                'entry_date' => $this->journalEntryDate,
                'description' => $this->journalDescription,
                'status' => 'posted',
                'lines' => $this->journalLines,
            ]);

            session()->flash('journal_success', 'Double-entry journal posted and posted to General Ledger.');
            $this->showingJournalModal = false;
        } catch (ValidationException $e) {
            $this->addError('journalLines', $e->getMessage());
        }
    }

    // --- Budgeting Operations ---
    public function openBudgetModal(): void
    {
        $this->budgetAccountId = Account::where('type', 'expense')->first()?->id;
        $this->budgetYear = date('Y');
        $this->budgetAllocated = 0.00;
        $this->showingBudgetModal = true;
    }

    public function saveBudget(): void
    {
        $this->validate([
            'budgetAccountId' => 'required|exists:accounts,id',
            'budgetYear' => 'required|integer',
            'budgetAllocated' => 'required|numeric|min:1',
        ]);

        Budget::updateOrCreate(
            ['account_id' => $this->budgetAccountId, 'fiscal_year' => $this->budgetYear],
            ['allocated_amount' => $this->budgetAllocated]
        );

        session()->flash('budget_success', 'Account budget limit saved.');
        $this->showingBudgetModal = false;
    }

    // --- Depreciation Scheduling ---
    public function depreciateAsset(string $assetId, FinanceService $financeService): void
    {
        $financeService->runStraightLineDepreciation($assetId);
        session()->flash('depr_success', 'Asset annual straight-line depreciation calculated and updated.');
    }

    public function render(FinanceService $financeService)
    {
        $accountsList = Account::orderBy('code')->get();
        $journalEntries = [];
        $cashBookLines = [];
        $trialBalance = [];
        $profitAndLoss = [];
        $balanceSheet = [];
        $budgetsList = [];
        $assetsList = [];

        if ($this->activeTab === 'journal') {
            $journalEntries = JournalEntry::with('lines.account')
                ->when($this->search, function ($q) {
                    $q->where('reference_number', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                })
                ->orderBy('entry_date', 'desc')
                ->paginate(10);
        } elseif ($this->activeTab === 'cashbook') {
            // Fetch all lines belonging to Cash (1010) or Bank (1020) accounts
            $cashBookLines = JournalLine::with(['entry', 'account'])
                ->whereHas('account', function ($q) {
                    $q->whereIn('code', ['1010', '1020']);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($this->activeTab === 'statements') {
            $trialBalance = $financeService->getTrialBalance();
            $profitAndLoss = $financeService->getProfitLoss();
            $balanceSheet = $financeService->getBalanceSheet();
        } elseif ($this->activeTab === 'assets') {
            $budgetsList = Budget::with('account')->get()->all();
            $assetsList = DepreciationSchedule::orderBy('asset_name')->get()->all();
        }

        return view('livewire.admin.finance-manager', [
            'accountsList' => $accountsList,
            'journalEntries' => $journalEntries,
            'cashBookLines' => $cashBookLines,
            'trialBalance' => $trialBalance,
            'profitAndLoss' => $profitAndLoss,
            'balanceSheet' => $balanceSheet,
            'budgetsList' => $budgetsList,
            'assetsList' => $assetsList,
        ])->layout('layouts.app');
    }
}
