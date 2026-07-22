<?php

namespace App\Repositories\Contracts;

use App\Models\Account;
use App\Models\Budget;
use App\Models\DepreciationSchedule;
use App\Models\JournalEntry;
use Illuminate\Pagination\LengthAwarePaginator;

interface FinanceRepositoryInterface
{
    public function createAccount(array $data): Account;

    public function getAccountsList(): array;

    public function postJournalEntry(array $data): JournalEntry;

    public function getJournalEntries(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function recordBudget(array $data): Budget;

    public function updateBudgetSpend(string $accountId, float $amount): bool;

    public function getBudgets(): array;

    public function createAssetSchedule(array $data): DepreciationSchedule;

    public function getAssets(): array;
}
