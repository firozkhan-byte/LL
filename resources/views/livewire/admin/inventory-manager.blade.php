<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ __('Real-time Inventory Control') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Track inventory ledgers, log damaged/lost adjustments, and run FIFO/LIFO valuation comparisons.') }}
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <div>
                <select wire:model.live="selectedWarehouseId" class="block rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-2">
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->code }})</option>
                    @endforeach
                </select>
            </div>
            @if($activeTab === 'stock')
                <button wire:click="openAdjModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer">
                    {{ __('Log Adjustment') }}
                </button>
            @endif
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('message'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-lg flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Tabs Nav -->
    <div class="border-b border-gray-200 dark:border-gray-700 flex space-x-8">
        <button wire:click="changeTab('stock')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition {{ $activeTab === 'stock' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('1. Real-time Stock') }}
        </button>
        <button wire:click="changeTab('ledger')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition {{ $activeTab === 'ledger' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('2. Stock Ledger') }}
        </button>
        <button wire:click="changeTab('valuation')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition {{ $activeTab === 'valuation' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('3. Valuation Costing') }}
        </button>
    </div>

    <!-- Active Tab Panel Content -->
    <div>
        @if($activeTab === 'stock')
            <!-- Real-time Stock Cards Grid -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Product Profile') }}</th>
                                <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('SKU') }}</th>
                                <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Batch Number') }}</th>
                                <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Reserved Qty') }}</th>
                                <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Available Stock') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            @forelse($stockCard as $card)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $card['name'] }}</td>
                                    <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $card['sku'] }}</td>
                                    <td class="px-6 py-4 font-mono text-xs text-gray-400">{{ $card['batch_number'] ?? __('N/A') }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ number_format($card['reserved_qty'], 2) }} {{ __('units') }}</td>
                                    <td class="px-6 py-4 font-extrabold text-gray-950 dark:text-white">
                                        {{ number_format($card['available_qty'], 2) }} {{ __('units') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 italic">{{ __('No stock cards allocated.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif($activeTab === 'ledger')
            <!-- Central Ledger Timeline view -->
            <div class="space-y-4">
                <!-- Ledger Filters -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="search" :value="__('Search Ledger')" />
                        <x-text-input wire:model.live="search" id="search" type="text" class="mt-1 block w-full" placeholder="Search product name..." />
                    </div>
                    <div>
                        <x-input-label for="txFilter" :value="__('Transaction Type')" />
                        <select wire:model.live="txFilter" id="txFilter" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2">
                            <option value="">{{ __('All Types') }}</option>
                            <option value="opening">{{ __('Opening Balance') }}</option>
                            <option value="purchase">{{ __('Purchases') }}</option>
                            <option value="sale">{{ __('Sales') }}</option>
                            <option value="adjustment_add">{{ __('Adjustments (Add)') }}</option>
                            <option value="adjustment_remove">{{ __('Adjustments (Remove)') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Ledger Audit Logs Table -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Date & Time') }}</th>
                                    <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Product') }}</th>
                                    <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Transaction Type') }}</th>
                                    <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Quantity Change') }}</th>
                                    <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Running Balance') }}</th>
                                    <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Unit Cost') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                @forelse($ledgerLogs as $log)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-6 py-4 text-gray-450 dark:text-gray-400 font-mono text-xs">{{ $log->created_at }}</td>
                                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $log->product->name }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                                @if(in_array($log->transaction_type, ['opening', 'purchase', 'adjustment_add'])) bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-300
                                                @else bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-300 @endif">
                                                {{ str_replace('_', ' ', $log->transaction_type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-bold @if($log->quantity > 0) text-green-600 @else text-rose-600 @endif">
                                            {{ $log->quantity > 0 ? '+' : '' }}{{ number_format($log->quantity, 2) }}
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ number_format($log->balance_after, 2) }} {{ __('units') }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-500">₹{{ number_format($log->unit_price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 italic">{{ __('No ledger audit trails registered.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t dark:border-gray-700">{{ $ledgerLogs->links() }}</div>
                </div>
            </div>

        @elseif($activeTab === 'valuation')
            <!-- Valuation Costing engine side-by-side comparators -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- FIFO card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-3 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-bl-xl text-xs font-bold uppercase tracking-widest">{{ __('FIFO') }}</div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider">{{ __('First In First Out') }}</h3>
                    <div class="text-3xl font-black text-gray-900 dark:text-white">₹{{ number_format($valuationTotals['fifo'], 2) }}</div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        {{ __('Assumes the oldest inventory items are sold first. During inflation, FIFO yields a higher ending inventory asset value.') }}
                    </p>
                </div>

                <!-- LIFO card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-3 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-bl-xl text-xs font-bold uppercase tracking-widest">{{ __('LIFO') }}</div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider">{{ __('Last In First Out') }}</h3>
                    <div class="text-3xl font-black text-gray-900 dark:text-white">₹{{ number_format($valuationTotals['lifo'], 2) }}</div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        {{ __('Assumes the newest inventory items are sold first. Often used in tax audits to match current costs against current revenues.') }}
                    </p>
                </div>

                <!-- WAC card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-3 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-bl-xl text-xs font-bold uppercase tracking-widest">{{ __('WAC') }}</div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider">{{ __('Weighted Average') }}</h3>
                    <div class="text-3xl font-black text-gray-900 dark:text-white">₹{{ number_format($valuationTotals['wac'], 2) }}</div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        {{ __('Computes average unit purchase rate to value ending stock. Evens out sharp price swings over the procurement cycle.') }}
                    </p>
                </div>
            </div>
        @endif
    </div>

    <!-- Inventory Adjustments modal -->
    @if($showingAdjModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="$set('showingAdjModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border dark:border-gray-700">
                    <form wire:submit="saveAdjustment">
                        <div class="px-6 py-4 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Log Stock Adjustment (Write-offs)') }}</h3>
                        </div>

                        <div class="p-6 space-y-4 max-h-[500px] overflow-y-auto">
                            <div>
                                <x-input-label for="adj-reason" :value="__('Write-off Reason')" />
                                <select wire:model="adjReason" id="adj-reason" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2" required>
                                    <option value="damaged">{{ __('Damaged / Broken Bottles') }}</option>
                                    <option value="lost">{{ __('Lost in Transit / Missing') }}</option>
                                    <option value="theft">{{ __('Theft / Pilferage') }}</option>
                                    <option value="cycle_count">{{ __('Cycle Counting Correction') }}</option>
                                    <option value="write_off">{{ __('General Write-off') }}</option>
                                </select>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Line Items') }}</h4>
                                    <button type="button" wire:click="addAdjItem" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">+ Add Line</button>
                                </div>

                                @foreach($adjItems as $index => $item)
                                    <div class="grid grid-cols-4 gap-2 items-center bg-gray-50 dark:bg-gray-900 p-3 rounded-lg border dark:border-gray-800">
                                        <select wire:model="adjItems.{{ $index }}.product_id" class="col-span-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" required>
                                            <option value="">-- {{ __('Select Product') }} --</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                        <select wire:model="adjItems.{{ $index }}.adjustment_type" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" required>
                                            <option value="decrement">{{ __('Decrement (-)') }}</option>
                                            <option value="increment">{{ __('Increment (+)') }}</option>
                                        </select>
                                        <div class="flex items-center justify-between">
                                            <input type="number" wire:model="adjItems.{{ $index }}.quantity" class="w-20 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" placeholder="Qty" required />
                                            <button type="button" wire:click="removeAdjItem({{ $index }})" class="text-red-500 hover:text-red-700">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div>
                                <x-input-label for="adj-remarks" :value="__('Remarks')" />
                                <textarea wire:model="adjRemarks" id="adj-remarks" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2"></textarea>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t dark:border-gray-700">
                            <x-primary-button type="submit">{{ __('Save Adjustment') }}</x-primary-button>
                            <x-secondary-button wire:click="$set('showingAdjModal', false)">{{ __('Cancel') }}</x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
