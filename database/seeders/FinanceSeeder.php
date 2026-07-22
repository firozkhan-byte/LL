<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Chart of Accounts
        $accounts = [
            ['id' => Str::uuid()->toString(), 'code' => '1010', 'name' => 'Cash Account', 'type' => 'asset'],
            ['id' => Str::uuid()->toString(), 'code' => '1020', 'name' => 'Bank Account', 'type' => 'asset'],
            ['id' => Str::uuid()->toString(), 'code' => '1035', 'name' => 'Inventory Asset', 'type' => 'asset'],
            ['id' => Str::uuid()->toString(), 'code' => '1200', 'name' => 'Accounts Receivable', 'type' => 'asset'],
            ['id' => Str::uuid()->toString(), 'code' => '2100', 'name' => 'Accounts Payable', 'type' => 'liability'],
            ['id' => Str::uuid()->toString(), 'code' => '3000', 'name' => 'Owner Equity', 'type' => 'equity'],
            ['id' => Str::uuid()->toString(), 'code' => '4000', 'name' => 'Sales Revenue', 'type' => 'revenue'],
            ['id' => Str::uuid()->toString(), 'code' => '5000', 'name' => 'Purchase COGS', 'type' => 'expense'],
            ['id' => Str::uuid()->toString(), 'code' => '6100', 'name' => 'Utility Expense', 'type' => 'expense'],
        ];

        foreach ($accounts as $acc) {
            DB::table('accounts')->insert([
                'id' => $acc['id'],
                'code' => $acc['code'],
                'name' => $acc['name'],
                'type' => $acc['type'],
                'parent_id' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Mapping codes to IDs for easier references
        $accMap = DB::table('accounts')->pluck('id', 'code')->toArray();

        // 2. Seed Opening Balance Journal Entry
        $jeId = Str::uuid()->toString();
        DB::table('journal_entries')->insert([
            'id' => $jeId,
            'entry_date' => now()->startOfYear()->format('Y-m-d'),
            'reference_number' => 'JE-2026-0001',
            'description' => 'Opening balance equity and cash deposits',
            'status' => 'posted',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Debit Cash ₹500,000
        DB::table('journal_lines')->insert([
            'id' => Str::uuid()->toString(),
            'journal_entry_id' => $jeId,
            'account_id' => $accMap['1010'],
            'debit' => 500000.00,
            'credit' => 0.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Credit Equity ₹500,000
        DB::table('journal_lines')->insert([
            'id' => Str::uuid()->toString(),
            'journal_entry_id' => $jeId,
            'account_id' => $accMap['3000'],
            'debit' => 0.00,
            'credit' => 500000.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Seed Budget
        DB::table('budgets')->insert([
            'id' => Str::uuid()->toString(),
            'account_id' => $accMap['6100'],
            'fiscal_year' => 2026,
            'allocated_amount' => 120000.00,
            'spent_amount' => 45000.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Seed Depreciation Schedules
        DB::table('depreciation_schedules')->insert([
            'id' => Str::uuid()->toString(),
            'asset_name' => 'Mumbai Depot Delivery Van',
            'purchase_cost' => 800000.00,
            'salvage_value' => 100000.00,
            'useful_life_years' => 5,
            'depreciation_method' => 'straight_line',
            'current_value' => 800000.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
