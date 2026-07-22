<?php

namespace App\Repositories\Eloquent;

use App\Models\Account;
use App\Models\Budget;
use App\Models\DepreciationSchedule;
use App\Models\JournalEntry;
use App\Repositories\Contracts\FinanceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class FinanceRepository implements FinanceRepositoryInterface
{
    public function createAccount(array $data): Account
    {
        return Account::create($data);
    }

    public function getAccountsList(): array
    {
        return Account::orderBy('code')->get()->all();
    }

    public function postJournalEntry(array $data): JournalEntry
    {
        $lines = $data['lines'] ?? [];
        unset($data['lines']);

        // Double-entry validation: Sum of Debits must equal Sum of Credits
        $totalDebit = collect($lines)->sum(fn ($l) => floatval($l['debit']));
        $totalCredit = collect($lines)->sum(fn ($l) => floatval($l['credit']));

        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw ValidationException::withMessages([
                'lines' => ['Double-entry validation failed: Total Debits (₹'.number_format($totalDebit, 2).') must equal Total Credits (₹'.number_format($totalCredit, 2).').'],
            ]);
        }

        $entry = JournalEntry::create($data);
        foreach ($lines as $line) {
            $entry->lines()->create($line);

            // If it's an expense account, auto-update the budget spend count
            $account = Account::find($line['account_id']);
            if ($account && $account->type === 'expense' && floatval($line['debit']) > 0) {
                $this->updateBudgetSpend($account->id, floatval($line['debit']));
            }
        }

        return $entry;
    }

    public function getJournalEntries(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = JournalEntry::with('lines.account');

        if (! empty($filters['search'])) {
            $query->where('reference_number', 'like', "%{$filters['search']}%")
                ->orWhere('description', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('entry_date', 'desc')->paginate($perPage);
    }

    public function recordBudget(array $data): Budget
    {
        return Budget::create($data);
    }

    public function updateBudgetSpend(string $accountId, float $amount): bool
    {
        $budget = Budget::where('account_id', $accountId)
            ->where('fiscal_year', date('Y'))
            ->first();

        if ($budget) {
            $budget->increment('spent_amount', $amount);

            return true;
        }

        return false;
    }

    public function getBudgets(): array
    {
        return Budget::with('account')->orderBy('fiscal_year', 'desc')->get()->all();
    }

    public function createAssetSchedule(array $data): DepreciationSchedule
    {
        return DepreciationSchedule::create($data);
    }

    public function getAssets(): array
    {
        return DepreciationSchedule::orderBy('asset_name')->get()->all();
    }
}
