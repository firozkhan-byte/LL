<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ __('Corporate Finance & General Ledger') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Maintain Chart of Accounts, balanced double-entry journals, Trial Balance validation, Profit & Loss reports, budgets, and asset depreciation.') }}
            </p>
        </div>
        <div class="flex gap-2">
            <button wire:click="openAccountModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Add GL Account') }}
            </button>
            <button wire:click="openJournalModal" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                </svg>
                {{ __('Post Journal Entry') }}
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('account_success'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-lg flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('account_success') }}</span>
        </div>
    @endif
    @if (session()->has('journal_success'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-lg flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('journal_success') }}</span>
        </div>
    @endif

    <!-- Tabs Nav -->
    <div class="border-b border-gray-200 dark:border-gray-700 flex space-x-8 overflow-x-auto">
        <button wire:click="setTab('accounts')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'accounts' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Chart of Accounts') }}
        </button>
        <button wire:click="setTab('journal')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'journal' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('General Ledger Journals') }}
        </button>
        <button wire:click="setTab('cashbook')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'cashbook' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Cash & Bank Books') }}
        </button>
        <button wire:click="setTab('statements')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'statements' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Financial Statements') }}
        </button>
        <button wire:click="setTab('assets')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'assets' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Assets & Budgets') }}
        </button>
    </div>

    <!-- Main Container -->
    <div class="space-y-6">
        @if ($activeTab === 'accounts')
            <!-- Chart of Accounts Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Account Code</th>
                                <th class="px-6 py-3">Account Name</th>
                                <th class="px-6 py-3">GL Classification Type</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($accountsList as $acc)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors">
                                    <td class="px-6 py-4 font-mono text-sm text-indigo-600 dark:text-indigo-400 font-bold">{{ $acc->code }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">{{ $acc->name }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full uppercase
                                            {{ $acc->type === 'asset' ? 'bg-blue-100 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400' : '' }}
                                            {{ $acc->type === 'liability' ? 'bg-red-100 dark:bg-red-950/40 text-red-700 dark:text-red-400' : '' }}
                                            {{ $acc->type === 'equity' ? 'bg-purple-100 dark:bg-purple-950/40 text-purple-700 dark:text-purple-400' : '' }}
                                            {{ $acc->type === 'revenue' ? 'bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-400' : '' }}
                                            {{ $acc->type === 'expense' ? 'bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400' : '' }}
                                        ">
                                            {{ $acc->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-105 text-green-700 dark:text-green-400">
                                            {{ ucfirst($acc->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No General Ledger accounts defined.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if ($activeTab === 'journal')
            <!-- Search Controls -->
            <div class="flex bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex-1 max-w-md">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by Ref # or Description..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Reference #</th>
                                <th class="px-6 py-3">Post Date</th>
                                <th class="px-6 py-3">Description</th>
                                <th class="px-6 py-3">Journal Entry Lines</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($journalEntries as $entry)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors">
                                    <td class="px-6 py-4 font-mono text-sm text-indigo-600 dark:text-indigo-400 font-bold align-top">{{ $entry->reference_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 align-top">{{ $entry->entry_date }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white align-top font-semibold">{{ $entry->description }}</td>
                                    <td class="px-6 py-4 text-xs font-mono">
                                        <div class="space-y-1">
                                            @foreach ($entry->lines as $line)
                                                <div class="flex justify-between gap-4 text-gray-800 dark:text-gray-300">
                                                    <span>{{ $line->account->name }}</span>
                                                    <span>
                                                        @if ($line->debit > 0)
                                                            <span class="text-indigo-600 dark:text-indigo-400">Dr ₹{{ number_format($line->debit, 2) }}</span>
                                                        @else
                                                            <span class="text-emerald-600 dark:text-emerald-400">Cr ₹{{ number_format($line->credit, 2) }}</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center align-top">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-400">
                                            {{ ucfirst($entry->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No journal entries found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div>
                {{ $journalEntries->links() }}
            </div>
        @endif

        @if ($activeTab === 'cashbook')
            <!-- Cash/Bank Ledger Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Ref Code</th>
                                <th class="px-6 py-3">Book Type</th>
                                <th class="px-6 py-3">Description</th>
                                <th class="px-6 py-3 text-right">Debit (Incoming)</th>
                                <th class="px-6 py-3 text-right">Credit (Outgoing)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($cashBookLines as $line)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $line->entry->entry_date }}</td>
                                    <td class="px-6 py-4 font-mono text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ $line->entry->reference_number }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-2 py-0.5 text-xs font-bold uppercase rounded-full bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200">
                                            {{ $line->account->code === '1010' ? 'Cash Book' : 'Bank Book' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-semibold">{{ $line->entry->description }}</td>
                                    <td class="px-6 py-4 text-right font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ $line->debit > 0 ? '₹' . number_format($line->debit, 2) : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $line->credit > 0 ? '₹' . number_format($line->credit, 2) : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No Cash or Bank entries on record.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div>
                {{ $cashBookLines->links() }}
            </div>
        @endif

        @if ($activeTab === 'statements')
            <!-- Side-by-side Financial Statements -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Trial Balance -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                        <h3 class="text-lg font-bold text-gray-950 dark:text-white">General Trial Balance</h3>
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $trialBalance['is_balanced'] ? 'bg-green-105 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-400' }}">
                            {{ $trialBalance['is_balanced'] ? 'Balanced' : 'Out of Balance' }}
                        </span>
                    </div>

                    <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-[300px] overflow-y-auto pr-1">
                        @foreach ($trialBalance['lines'] as $line)
                            <div class="flex justify-between py-2 text-sm">
                                <span class="font-mono text-gray-500 mr-2">{{ $line['account_code'] }}</span>
                                <span class="flex-1 text-gray-950 dark:text-white">{{ $line['account_name'] }}</span>
                                <div class="flex gap-4 font-mono">
                                    <span class="w-24 text-right text-indigo-600 dark:text-indigo-400">{{ $line['debit'] > 0 ? 'Dr ₹' . number_format($line['debit'], 2) : '-' }}</span>
                                    <span class="w-24 text-right text-emerald-600 dark:text-emerald-400">{{ $line['credit'] > 0 ? 'Cr ₹' . number_format($line['credit'], 2) : '-' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-between pt-3 border-t border-gray-200 dark:border-gray-700 text-sm font-black text-gray-900 dark:text-white font-mono">
                        <span>Total Trial Summary</span>
                        <div class="flex gap-4">
                            <span class="w-24 text-right">₹{{ number_format($trialBalance['total_debit'], 2) }}</span>
                            <span class="w-24 text-right">₹{{ number_format($trialBalance['total_credit'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Profit & Loss Statement -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                        <h3 class="text-lg font-bold text-gray-950 dark:text-white">Income Statement (P&L)</h3>
                        <span class="text-xs text-gray-500">Year-To-Date (YTD)</span>
                    </div>

                    <div class="space-y-4">
                        <!-- Revenues -->
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-2">Incoming Revenue</span>
                            @foreach ($profitAndLoss['revenues'] as $rev)
                                <div class="flex justify-between text-sm py-1 border-b border-gray-100 dark:border-gray-700/35">
                                    <span class="text-gray-950 dark:text-white">{{ $rev['name'] }}</span>
                                    <span class="font-mono text-gray-900 dark:text-white">₹{{ number_format($rev['amount'], 2) }}</span>
                                </div>
                            @endforeach
                            <div class="flex justify-between text-sm font-bold text-indigo-600 dark:text-indigo-400 mt-2">
                                <span>Total Revenue</span>
                                <span class="font-mono">₹{{ number_format($profitAndLoss['total_revenue'], 2) }}</span>
                            </div>
                        </div>

                        <!-- Expenses -->
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-2">Operating Expenses</span>
                            @foreach ($profitAndLoss['expenses'] as $exp)
                                <div class="flex justify-between text-sm py-1 border-b border-gray-100 dark:border-gray-700/35">
                                    <span class="text-gray-950 dark:text-white">{{ $exp['name'] }}</span>
                                    <span class="font-mono text-gray-900 dark:text-white">₹{{ number_format($exp['amount'], 2) }}</span>
                                </div>
                            @endforeach
                            <div class="flex justify-between text-sm font-bold text-amber-600 dark:text-amber-400 mt-2">
                                <span>Total Expenses</span>
                                <span class="font-mono">₹{{ number_format($profitAndLoss['total_expense'], 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between pt-3 border-t border-gray-200 dark:border-gray-700 text-base font-black text-green-600 dark:text-green-400 font-mono">
                        <span>Net Profit / (Loss)</span>
                        <span>₹{{ number_format($profitAndLoss['net_profit'], 2) }}</span>
                    </div>
                </div>

                <!-- Balance Sheet Statement -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-6">
                    <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                        <h3 class="text-lg font-bold text-gray-950 dark:text-white">Statement of Financial Position (Balance Sheet)</h3>
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $balanceSheet['is_balanced'] ? 'bg-green-105 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-400' }}">
                            {{ $balanceSheet['is_balanced'] ? 'Assets Balanced' : 'Out of Balance' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Assets -->
                        <div class="space-y-4">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block border-b border-gray-200 dark:border-gray-700 pb-1">Assets (Dr)</span>
                            <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                @foreach ($balanceSheet['assets'] as $asset)
                                    <div class="flex justify-between text-sm py-2">
                                        <span class="text-gray-900 dark:text-white">{{ $asset['name'] }}</span>
                                        <span class="font-mono text-gray-800 dark:text-slate-200">₹{{ number_format($asset['amount'], 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex justify-between font-black text-gray-950 dark:text-white text-sm pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span>Total Assets</span>
                                <span class="font-mono">₹{{ number_format($balanceSheet['total_assets'], 2) }}</span>
                            </div>
                        </div>

                        <!-- Liabilities & Equity -->
                        <div class="space-y-4">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block border-b border-gray-200 dark:border-gray-700 pb-1">Liabilities & Equities (Cr)</span>
                            <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                @foreach ($balanceSheet['liabilities'] as $liab)
                                    <div class="flex justify-between text-sm py-2">
                                        <span class="text-gray-900 dark:text-white">{{ $liab['name'] }}</span>
                                        <span class="font-mono text-gray-800 dark:text-slate-200">₹{{ number_format($liab['amount'], 2) }}</span>
                                    </div>
                                @endforeach
                                @foreach ($balanceSheet['equity'] as $eq)
                                    <div class="flex justify-between text-sm py-2">
                                        <span class="text-gray-900 dark:text-white">{{ $eq['name'] }}</span>
                                        <span class="font-mono text-gray-800 dark:text-slate-200">₹{{ number_format($eq['amount'], 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex justify-between font-black text-gray-950 dark:text-white text-sm pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span>Total Liabilities & Equity</span>
                                <span class="font-mono">₹{{ number_format($balanceSheet['total_liabilities_and_equity'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($activeTab === 'assets')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Budgets Limits Tracker -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                        <h3 class="text-lg font-bold text-gray-950 dark:text-white">Operating Expense Budgets</h3>
                        <button type="button" wire:click="openBudgetModal" class="text-xs px-2.5 py-1.5 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 font-bold rounded cursor-pointer transition-colors">
                            Set Budget Allocation
                        </button>
                    </div>

                    @if (session()->has('budget_success'))
                        <p class="text-xs text-green-600 font-bold">{{ session('budget_success') }}</p>
                    @endif

                    <div class="space-y-4">
                        @forelse ($budgetsList as $budget)
                            <div>
                                <div class="flex justify-between text-xs font-semibold mb-1">
                                    <span class="text-gray-900 dark:text-white">{{ $budget->account->name }} (FY {{ $budget->fiscal_year }})</span>
                                    <span class="text-gray-500">₹{{ number_format($budget->spent_amount, 2) }} / ₹{{ number_format($budget->allocated_amount, 2) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-750 rounded-full h-2">
                                    @php
                                        $percent = min(100, ($budget->spent_amount / $budget->allocated_amount) * 100);
                                    @endphp
                                    <div class="h-2 rounded-full {{ $percent >= 90 ? 'bg-red-500' : ($percent >= 70 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-sm text-center py-6">No budget allocations recorded.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Depreciation Schedules -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                        <h3 class="text-lg font-bold text-gray-950 dark:text-white">Fixed Assets & Depreciation</h3>
                        @if (session()->has('depr_success'))
                            <span class="text-xs text-green-600 font-bold">{{ session('depr_success') }}</span>
                        @endif
                    </div>

                    <div class="divide-y divide-gray-150 dark:divide-gray-700">
                        @forelse ($assetsList as $asset)
                            <div class="py-3 flex justify-between items-center gap-4 text-sm">
                                <div>
                                    <span class="font-bold text-gray-950 dark:text-white block">{{ $asset->asset_name }}</span>
                                    <span class="text-xs text-gray-500 block">Cost: ₹{{ number_format($asset->purchase_cost, 2) }} | Salvage: ₹{{ number_format($asset->salvage_value, 2) }} | Life: {{ $asset->useful_life_years }} years</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-mono text-gray-850 dark:text-slate-200 block font-bold">Current Book Value: ₹{{ number_format($asset->current_value, 2) }}</span>
                                    @if ($asset->current_value > $asset->salvage_value)
                                        <button wire:click="depreciateAsset('{{ $asset->id }}')" class="mt-1 text-xs text-indigo-650 hover:text-indigo-800 dark:text-indigo-400 cursor-pointer font-bold">Depreciate Year</button>
                                    @else
                                        <span class="text-[10px] text-gray-400 italic">Fully Depreciated</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-sm text-center py-6">No fixed assets on record.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- ADD GL ACCOUNT MODAL -->
    @if ($showingAccountModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-md rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Add New GL Account</h3>
                    <button wire:click="$set('showingAccountModal', false)" class="text-gray-400 hover:text-gray-650 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Account Code</label>
                        <input type="text" wire:model="newAccountCode" placeholder="e.g. 1015, 6200" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        @error('newAccountCode') <p class="text-xs text-red-650 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Account Name</label>
                        <input type="text" wire:model="newAccountName" placeholder="e.g. Petty Cash, Office rent expense" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        @error('newAccountName') <p class="text-xs text-red-650 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">GL Account Type</label>
                        <select wire:model="newAccountType" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            <option value="asset">Asset</option>
                            <option value="liability">Liability</option>
                            <option value="equity">Equity</option>
                            <option value="revenue">Revenue</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showingAccountModal', false)" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg cursor-pointer">Cancel</button>
                    <button type="button" wire:click="saveAccount" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg cursor-pointer transition-colors shadow">Save Account</button>
                </div>
            </div>
        </div>
    @endif

    <!-- POST BALANCED JOURNAL ENTRY MODAL -->
    @if ($showingJournalModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-3xl rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200 flex flex-col max-h-[90vh]">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Post Balanced Journal Entry</h3>
                    <button wire:click="$set('showingJournalModal', false)" class="text-gray-400 hover:text-gray-650 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-6 overflow-y-auto flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Entry Date</label>
                            <input type="date" wire:model="journalEntryDate" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Reference Memo</label>
                            <input type="text" wire:model="journalDescription" placeholder="Description of GL adjustments..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        </div>
                    </div>

                    <!-- Double entry lines workspace -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Debit & Credit Entry Lines</span>
                            <button type="button" wire:click="addJournalLine" class="text-xs px-3 py-1.5 bg-indigo-105 hover:bg-indigo-200 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 font-bold rounded cursor-pointer flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Line
                            </button>
                        </div>

                        <div class="space-y-3">
                            @foreach ($journalLines as $idx => $line)
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 bg-gray-50 dark:bg-gray-850/40 p-3 rounded-lg border border-gray-200 dark:border-gray-800 items-center">
                                    <!-- Select Account -->
                                    <div class="md:col-span-2">
                                        <select wire:model="journalLines.{{ $idx }}.account_id" class="w-full rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-1.5 px-3">
                                            @foreach ($accountsList as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Debit -->
                                    <div>
                                        <input type="number" step="0.01" min="0" wire:model.live="journalLines.{{ $idx }}.debit" placeholder="Debit (Dr)" class="w-full rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-1.5 px-3 text-right">
                                    </div>

                                    <!-- Credit / Remove -->
                                    <div class="flex items-center gap-2">
                                        <input type="number" step="0.01" min="0" wire:model.live="journalLines.{{ $idx }}.credit" placeholder="Credit (Cr)" class="w-full rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-1.5 px-3 text-right">
                                        <button type="button" wire:click="removeJournalLine({{ $idx }})" class="text-red-650 hover:text-red-500 p-1 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('journalLines')
                            <p class="text-xs text-red-650 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-between items-center">
                    <div>
                        @php
                            $drSum = collect($journalLines)->sum(fn($l) => floatval($l['debit'] ?? 0));
                            $crSum = collect($journalLines)->sum(fn($l) => floatval($l['credit'] ?? 0));
                        @endphp
                        <span class="text-gray-500 dark:text-gray-400 text-xs block uppercase font-bold">Total Trial Post</span>
                        <span class="text-sm font-mono font-bold text-gray-800 dark:text-slate-200 block">Debits: ₹{{ number_format($drSum, 2) }}</span>
                        <span class="text-sm font-mono font-bold text-gray-800 dark:text-slate-200 block">Credits: ₹{{ number_format($crSum, 2) }}</span>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" wire:click="$set('showingJournalModal', false)" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 text-gray-700 dark:text-gray-300 font-semibold rounded-lg cursor-pointer">Cancel</button>
                        <button type="button" wire:click="saveJournal" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg cursor-pointer shadow">Post Transaction</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- SET BUDGET LIMIT MODAL -->
    @if ($showingBudgetModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-md rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Allocate GL Expense Budget</h3>
                    <button wire:click="$set('showingBudgetModal', false)" class="text-gray-400 hover:text-gray-650 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">GL Account (Expense Classification)</label>
                        <select wire:model="budgetAccountId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            @foreach ($accountsList as $acc)
                                @if ($acc->type === 'expense')
                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Fiscal Year</label>
                        <input type="number" wire:model="budgetYear" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 text-center">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Allocated Budget Limit (₹)</label>
                        <input type="number" wire:model="budgetAllocated" placeholder="Allocated amount..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 text-right">
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showingBudgetModal', false)" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg cursor-pointer">Cancel</button>
                    <button type="button" wire:click="saveBudget" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg cursor-pointer transition-colors shadow">Save Allocation</button>
                </div>
            </div>
        </div>
    @endif
</div>
