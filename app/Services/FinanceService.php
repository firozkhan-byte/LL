<?php

namespace App\Services;

use App\Models\Account;
use App\Models\DepreciationSchedule;
use App\Models\JournalLine;
use App\Repositories\Contracts\FinanceRepositoryInterface;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    protected FinanceRepositoryInterface $financeRepo;

    public function __construct(FinanceRepositoryInterface $financeRepo)
    {
        $this->financeRepo = $financeRepo;
    }

    public function createAccount(array $data)
    {
        return $this->financeRepo->createAccount($data);
    }

    public function postJournal(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->financeRepo->postJournalEntry($data);
        });
    }

    /**
     * Compute Trial Balance summary metrics.
     */
    public function getTrialBalance(): array
    {
        $accounts = Account::all();
        $trialLines = [];
        $totalDebitSum = 0.00;
        $totalCreditSum = 0.00;

        foreach ($accounts as $acc) {
            $debits = JournalLine::where('account_id', $acc->id)->sum('debit');
            $credits = JournalLine::where('account_id', $acc->id)->sum('credit');

            $balance = 0.00;
            $balanceType = 'debit'; // Assets & Expenses debit positive; Liabilities, Equity, Revenue credit positive

            if ($acc->type === 'asset' || $acc->type === 'expense') {
                $balance = $debits - $credits;
                if ($balance < 0) {
                    $balance = abs($balance);
                    $balanceType = 'credit';
                }
            } else {
                $balance = $credits - $debits;
                if ($balance < 0) {
                    $balance = abs($balance);
                    $balanceType = 'debit';
                }
            }

            if ($balance > 0) {
                if ($balanceType === 'debit') {
                    $totalDebitSum += $balance;
                } else {
                    $totalCreditSum += $balance;
                }

                $trialLines[] = [
                    'account_code' => $acc->code,
                    'account_name' => $acc->name,
                    'debit' => $balanceType === 'debit' ? $balance : 0.00,
                    'credit' => $balanceType === 'credit' ? $balance : 0.00,
                ];
            }
        }

        return [
            'lines' => $trialLines,
            'total_debit' => $totalDebitSum,
            'total_credit' => $totalCreditSum,
            'is_balanced' => abs($totalDebitSum - $totalCreditSum) < 0.01,
        ];
    }

    /**
     * Compute P&L (Profit & Loss) Statement.
     */
    public function getProfitLoss(): array
    {
        $revenueAccounts = Account::where('type', 'revenue')->get();
        $expenseAccounts = Account::where('type', 'expense')->get();

        $totalRevenue = 0.00;
        $revenueDetails = [];
        foreach ($revenueAccounts as $acc) {
            $val = JournalLine::where('account_id', $acc->id)->sum('credit') - JournalLine::where('account_id', $acc->id)->sum('debit');
            $totalRevenue += $val;
            $revenueDetails[] = ['name' => $acc->name, 'code' => $acc->code, 'amount' => $val];
        }

        $totalExpense = 0.00;
        $expenseDetails = [];
        foreach ($expenseAccounts as $acc) {
            $val = JournalLine::where('account_id', $acc->id)->sum('debit') - JournalLine::where('account_id', $acc->id)->sum('credit');
            $totalExpense += $val;
            $expenseDetails[] = ['name' => $acc->name, 'code' => $acc->code, 'amount' => $val];
        }

        $netProfit = $totalRevenue - $totalExpense;

        return [
            'revenues' => $revenueDetails,
            'expenses' => $expenseDetails,
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'net_profit' => $netProfit,
        ];
    }

    /**
     * Compute Balance Sheet Statement.
     */
    public function getBalanceSheet(): array
    {
        $assetAccounts = Account::where('type', 'asset')->get();
        $liabilityAccounts = Account::where('type', 'liability')->get();
        $equityAccounts = Account::where('type', 'equity')->get();

        $totalAssets = 0.00;
        $assetDetails = [];
        foreach ($assetAccounts as $acc) {
            $val = JournalLine::where('account_id', $acc->id)->sum('debit') - JournalLine::where('account_id', $acc->id)->sum('credit');
            $totalAssets += $val;
            $assetDetails[] = ['name' => $acc->name, 'code' => $acc->code, 'amount' => $val];
        }

        $totalLiabilities = 0.00;
        $liabilityDetails = [];
        foreach ($liabilityAccounts as $acc) {
            $val = JournalLine::where('account_id', $acc->id)->sum('credit') - JournalLine::where('account_id', $acc->id)->sum('debit');
            $totalLiabilities += $val;
            $liabilityDetails[] = ['name' => $acc->name, 'code' => $acc->code, 'amount' => $val];
        }

        // Add P&L Net profit into Equity retained earnings check dynamically
        $pl = $this->getProfitLoss();
        $retainedEarnings = $pl['net_profit'];

        $totalEquity = $retainedEarnings;
        $equityDetails = [];
        foreach ($equityAccounts as $acc) {
            $val = JournalLine::where('account_id', $acc->id)->sum('credit') - JournalLine::where('account_id', $acc->id)->sum('debit');
            $totalEquity += $val;
            $equityDetails[] = ['name' => $acc->name, 'code' => $acc->code, 'amount' => $val];
        }

        $equityDetails[] = ['name' => 'Retained Earnings (YTD P&L)', 'code' => 'YTD-PL', 'amount' => $retainedEarnings];

        return [
            'assets' => $assetDetails,
            'liabilities' => $liabilityDetails,
            'equity' => $equityDetails,
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'total_liabilities_and_equity' => $totalLiabilities + $totalEquity,
            'is_balanced' => abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01,
        ];
    }

    /**
     * Straight Line Depreciation Calculator.
     */
    public function runStraightLineDepreciation(string $assetId): bool
    {
        $asset = DepreciationSchedule::find($assetId);
        if (! $asset) {
            return false;
        }

        // SL Depreciation: (Cost - Salvage) / Useful Life
        $annualDepreciation = ($asset->purchase_cost - $asset->salvage_value) / $asset->useful_life_years;
        $asset->current_value = max($asset->salvage_value, $asset->current_value - $annualDepreciation);
        $asset->save();

        return true;
    }
}
