<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ __('Warehouse & Storage Locations') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Map physical storage locations, manage putaway tasks, and simulate barcode/QR scan reallocations.') }}
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
            @if($activeTab === 'transfers')
                <button wire:click="openTransferModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer">
                    {{ __('Initiate Transfer') }}
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
        <button wire:click="changeTab('map')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition {{ $activeTab === 'map' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('1. Hierarchical Map') }}
        </button>
        <button wire:click="changeTab('transfers')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition {{ $activeTab === 'transfers' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('2. Stock Transfers') }}
        </button>
        <button wire:click="changeTab('scanner')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition {{ $activeTab === 'scanner' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('3. Barcode Console') }}
        </button>
        <button wire:click="changeTab('counting')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition {{ $activeTab === 'counting' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('4. Cycle Counting') }}
        </button>
    </div>

    <!-- Active Tab Panel Content -->
    <div>
        @if($activeTab === 'map')
            <!-- Physical Map Tree View -->
            <div class="space-y-4">
                @forelse($racks as $rack)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-3 border-b dark:border-gray-700 flex justify-between items-center">
                            <div>
                                <span class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 rounded text-xs font-bold mr-2">{{ $rack['code'] }}</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ $rack['name'] }}</span>
                            </div>
                            <span class="text-xs text-gray-400">{{ count($rack['shelves']) }} {{ __('Shelves mapped') }}</span>
                        </div>

                        <div class="p-6 space-y-6">
                            @foreach($rack['shelves'] as $shelf)
                                <div class="border-l-4 border-indigo-500 pl-4 space-y-3">
                                    <div class="text-sm font-extrabold text-gray-850 dark:text-gray-200">{{ $shelf['name'] }} ({{ $shelf['code'] }})</div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($shelf['bins'] as $bin)
                                            @php
                                                $binInv = \App\Models\BinInventory::with('product')->where('bin_id', $bin['id'])->get();
                                            @endphp
                                            <div class="bg-gray-50 dark:bg-gray-900/40 p-4 rounded-xl border dark:border-gray-800 flex flex-col justify-between space-y-3">
                                                <div>
                                                    <div class="flex justify-between items-start">
                                                        <div class="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $bin['code'] }}</div>
                                                        <span class="text-[10px] text-gray-400">Cap: {{ $bin['capacity_weight'] }}kg</span>
                                                    </div>
                                                    <div class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">{{ $bin['name'] }}</div>

                                                    <!-- Bin contents list -->
                                                    <div class="mt-3 space-y-1.5">
                                                        @forelse($binInv as $inv)
                                                            <div class="flex justify-between text-xs border-b dark:border-gray-800 pb-1">
                                                                <span class="text-gray-700 dark:text-gray-300">{{ $inv->product->name }}</span>
                                                                <span class="font-extrabold text-gray-900 dark:text-white">{{ $inv->quantity }} {{ __('units') }}</span>
                                                            </div>
                                                        @empty
                                                            <span class="text-[10px] text-gray-400 italic block">{{ __('Empty Bin Allocation') }}</span>
                                                        @endforelse
                                                    </div>
                                                </div>
                                                
                                                <div class="pt-2 border-t dark:border-gray-800 flex justify-end">
                                                    <button wire:click="openPutawayModal('{{ $bin['id'] }}')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">
                                                        {{ __('+ Putaway Stock') }}
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-xl border dark:border-gray-700 text-center italic text-sm text-gray-500">
                        {{ __('No racks mapped under this warehouse.') }}
                    </div>
                @endforelse
            </div>

        @elseif($activeTab === 'transfers')
            <!-- Stock transfers lists -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Transfer Code') }}</th>
                                <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('From Warehouse') }}</th>
                                <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('To Warehouse') }}</th>
                                <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Total Items') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            @forelse($transfersList as $tx)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $tx->code }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $tx->fromWarehouse->name }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $tx->toWarehouse->name }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-300">
                                            {{ $tx->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">{{ $tx->items->count() }} {{ __('Lines') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 italic">{{ __('No transfers registered.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t dark:border-gray-700">{{ $transfersList->links() }}</div>
            </div>

        @elseif($activeTab === 'scanner')
            <!-- Simulated Barcode Console scanner -->
            <div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
                <div class="text-center space-y-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Virtual Scanner Terminal') }}</h3>
                    <p class="text-xs text-gray-400">{{ __('Simulate scanning product SKU barcodes and shelf QR tags.') }}</p>
                </div>

                <!-- Fake Camera scanning viewport graphic -->
                <div class="relative bg-gray-900 h-44 rounded-xl border border-gray-700 overflow-hidden flex items-center justify-center">
                    <div class="absolute inset-0 border-2 border-dashed border-indigo-500 opacity-30 animate-pulse"></div>
                    <div class="absolute w-full h-0.5 bg-red-500 top-1/2 left-0 shadow-lg animate-bounce"></div>
                    <span class="text-[10px] font-mono text-gray-500 uppercase tracking-widest z-10">{{ __('Waiting for scan read...') }}</span>
                </div>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="scannedBarcode" :value="__('Scan / Type Product Barcode (or SKU)')" />
                        <x-text-input wire:model="scannedBarcode" id="scannedBarcode" class="mt-1 block w-full text-sm font-mono" placeholder="e.g. SKU-JW-BLACK" />
                    </div>
                    <div>
                        <x-input-label for="scannedBinCode" :value="__('Scan / Type Storage Bin QR Code')" />
                        <x-text-input wire:model="scannedBinCode" id="scannedBinCode" class="mt-1 block w-full text-sm font-mono" placeholder="e.g. BIN-A1-S1-01" />
                    </div>
                    <button wire:click="runBarcodeLookup" class="w-full py-2 bg-gray-900 text-white rounded-lg text-sm font-bold hover:bg-gray-800 transition cursor-pointer">
                        {{ __('Simulate Scanner Lookup') }}
                    </button>
                </div>

                @if(session()->has('scanner_error'))
                    <div class="p-3 bg-red-50 dark:bg-red-950/20 text-red-700 text-xs rounded border dark:border-red-900/50">
                        {{ session('scanner_error') }}
                    </div>
                @endif

                @if($scanLookupResult)
                    <div class="p-4 bg-indigo-50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/50 rounded-xl space-y-3">
                        <h4 class="text-xs font-bold text-indigo-700 dark:text-indigo-400 uppercase tracking-wider">{{ __('Lookup Match Result') }}</h4>
                        
                        @if($scanLookupResult['type'] === 'match')
                            <div class="text-xs space-y-1 text-gray-700 dark:text-gray-300">
                                <div>Product: <span class="font-bold text-gray-900 dark:text-white">{{ $scanLookupResult['product_name'] }}</span></div>
                                <div>Bin: <span class="font-bold text-gray-900 dark:text-white">{{ $scanLookupResult['bin_name'] }} ({{ $scanLookupResult['bin_code'] }})</span></div>
                            </div>
                            <button wire:click="executeScanPutaway" class="w-full py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700 transition cursor-pointer">
                                {{ __('Execute Scanner Putaway (1 Unit)') }}
                            </button>
                        @elseif($scanLookupResult['type'] === 'product_only')
                            <div class="text-xs text-gray-700 dark:text-gray-300">
                                {{ __('Product identified:') }} <span class="font-bold text-gray-900 dark:text-white">{{ $scanLookupResult['product_name'] }}</span>. <br>
                                <span class="text-gray-400 italic">{{ __('Scan storage QR code to assign a location.') }}</span>
                            </div>
                        @elseif($scanLookupResult['type'] === 'bin_only')
                            <div class="text-xs text-gray-700 dark:text-gray-300">
                                {{ __('Storage location identified:') }} <span class="font-bold text-gray-900 dark:text-white">{{ $scanLookupResult['bin_name'] }} ({{ $scanLookupResult['bin_code'] }})</span>. <br>
                                <span class="text-gray-400 italic">{{ __('Scan product SKU barcode to record a putaway.') }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

        @elseif($activeTab === 'counting')
            <!-- Cycle Counting discrepancy auditor -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Bin Location') }}</th>
                                <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Product Name') }}</th>
                                <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('System Qty') }}</th>
                                <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-40">{{ __('Physical Count') }}</th>
                                <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">{{ __('Audit Adjust') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            @forelse($binInventoriesList as $inv)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4 font-mono text-xs font-bold text-gray-900 dark:text-white">{{ $inv->bin->code }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $inv->product->name }}</td>
                                    <td class="px-6 py-4 font-semibold text-gray-500">{{ $inv->quantity }} units</td>
                                    <td class="px-6 py-4">
                                        <input type="number" id="qty-{{ $inv->id }}" value="{{ $inv->quantity }}" class="w-24 rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-xs py-1" />
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button onclick="var input=document.getElementById('qty-{{ $inv->id }}'); @this.adjustCycleDiscrepancy('{{ $inv->id }}', input.value)" class="text-xs font-bold text-indigo-600 hover:text-indigo-900 cursor-pointer">
                                            {{ __('Post Adjustment') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 italic">{{ __('No bin inventories seeded.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t dark:border-gray-700">{{ $binInventoriesList->links() }}</div>
            </div>
        @endif
    </div>

    <!-- Putaway Form Modal -->
    @if($showingPutawayModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="$set('showingPutawayModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border dark:border-gray-700">
                    <form wire:submit="executePutaway">
                        <div class="px-6 py-4 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Execute Physical Putaway') }}</h3>
                        </div>

                        <div class="p-6 space-y-4">
                            <div>
                                <x-input-label for="putaway-prod" :value="__('Select Product')" />
                                <select wire:model="putawayProductId" id="putaway-prod" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2" required>
                                    <option value="">-- {{ __('Select') }} --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="putaway-qty" :value="__('Putaway Quantity')" />
                                <x-text-input wire:model="putawayQuantity" id="putaway-qty" type="number" class="mt-1 block w-full" required />
                            </div>
                            <div>
                                <x-input-label for="putaway-batch" :value="__('Batch Number')" />
                                <x-text-input wire:model="putawayBatch" id="putaway-batch" type="text" class="mt-1 block w-full" />
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t dark:border-gray-700">
                            <x-primary-button type="submit">{{ __('Complete Putaway') }}</x-primary-button>
                            <x-secondary-button wire:click="$set('showingPutawayModal', false)">{{ __('Cancel') }}</x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Stock Transfer Modal -->
    @if($showingTransferModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="$set('showingTransferModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border dark:border-gray-700">
                    <form wire:submit="saveTransfer">
                        <div class="px-6 py-4 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Stock Transfer Order') }}</h3>
                        </div>

                        <div class="p-6 space-y-4 max-h-[500px] overflow-y-auto">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="tx-from" :value="__('From Warehouse')" />
                                    <select wire:model="txFromWarehouseId" id="tx-from" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2" required>
                                        @foreach($warehouses as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="tx-to" :value="__('To Destination Warehouse')" />
                                    <select wire:model="txToWarehouseId" id="tx-to" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2" required>
                                        <option value="">-- {{ __('Select Destination') }} --</option>
                                        @foreach($warehouses as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Line Items') }}</h4>
                                    <button type="button" wire:click="addTxItem" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">+ Add Line</button>
                                </div>

                                @foreach($txItems as $index => $item)
                                    <div class="grid grid-cols-3 gap-2 items-center bg-gray-50 dark:bg-gray-900 p-3 rounded-lg border dark:border-gray-800">
                                        <select wire:model="txItems.{{ $index }}.product_id" class="col-span-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" required>
                                            <option value="">-- {{ __('Select Product') }} --</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="flex items-center justify-between">
                                            <input type="number" wire:model="txItems.{{ $index }}.quantity" class="w-20 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" placeholder="Qty" required />
                                            <button type="button" wire:click="removeTxItem({{ $index }})" class="text-red-500 hover:text-red-700">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t dark:border-gray-700">
                            <x-primary-button type="submit">{{ __('Execute Transfer') }}</x-primary-button>
                            <x-secondary-button wire:click="$set('showingTransferModal', false)">{{ __('Cancel') }}</x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
