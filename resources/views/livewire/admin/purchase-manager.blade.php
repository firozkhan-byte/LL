<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ __('Procurement & Purchase') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Manage requisitions, purchase orders, goods receipts, and vendor invoice settlements.') }}
            </p>
        </div>
        <div class="flex items-center space-x-2">
            @if($activeTab === 'requisitions')
                <button wire:click="openReqModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer">
                    {{ __('Create Requisition') }}
                </button>
            @elseif($activeTab === 'orders')
                <button wire:click="openPoModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer">
                    {{ __('Generate PO') }}
                </button>
            @elseif($activeTab === 'receipts')
                <button wire:click="openGrnModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer">
                    {{ __('Record GRN') }}
                </button>
            @elseif($activeTab === 'invoices')
                <button wire:click="openInvModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer">
                    {{ __('Record Invoice') }}
                </button>
            @endif
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
            <div class="p-3 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-lg">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <div>
                <span class="block text-xs text-gray-400 font-semibold uppercase">{{ __('Total POs') }}</span>
                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $metrics['total_orders'] }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-lg">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <span class="block text-xs text-gray-400 font-semibold uppercase">{{ __('Pending Receipts') }}</span>
                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $metrics['pending_receipts'] }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
            <div class="p-3 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-lg">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <span class="block text-xs text-gray-400 font-semibold uppercase">{{ __('Total Procurement') }}</span>
                <span class="text-2xl font-black text-gray-900 dark:text-white">₹{{ number_format($metrics['total_spent'], 2) }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
            <div class="p-3 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-lg">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <span class="block text-xs text-gray-400 font-semibold uppercase">{{ __('Overdue Invoices') }}</span>
                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $metrics['overdue_invoices'] }}</span>
            </div>
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
        <button wire:click="changeTab('requisitions')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition {{ $activeTab === 'requisitions' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('1. Requisitions') }}
        </button>
        <button wire:click="changeTab('orders')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition {{ $activeTab === 'orders' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('2. Purchase Orders') }}
        </button>
        <button wire:click="changeTab('receipts')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition {{ $activeTab === 'receipts' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('3. Receipts (GRN)') }}
        </button>
        <button wire:click="changeTab('invoices')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition {{ $activeTab === 'invoices' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('4. Invoices') }}
        </button>
    </div>

    <!-- Active Tab Panel Content -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if($activeTab === 'requisitions')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Code') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Requested By') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Needed By') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Items Count') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        @forelse($requisitionsList as $req)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $req->code }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $req->requester->name }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $req->needed_by_date }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-300">
                                        {{ $req->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-700 dark:text-gray-300">{{ $req->items->count() }} {{ __('Line items') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 italic">{{ __('No requisitions logged.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t dark:border-gray-700">{{ $requisitionsList->links() }}</div>

        @elseif($activeTab === 'orders')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('PO Code') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Supplier') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Date') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Terms') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Amount') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        @forelse($purchaseOrdersList as $po)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $po->code }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $po->supplier->name }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $po->po_date }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $po->payment_terms }}</td>
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">₹{{ number_format($po->total_amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                        @if($po->status === 'received') bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-300
                                        @else bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300 @endif">
                                        {{ $po->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 italic">{{ __('No purchase orders logged.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t dark:border-gray-700">{{ $purchaseOrdersList->links() }}</div>

        @elseif($activeTab === 'receipts')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('GRN Code') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('PO Linked') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Received Date') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Receiver') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        @forelse($grnList as $grn)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $grn->code }}</td>
                                <td class="px-6 py-4 font-mono text-gray-500">{{ $grn->purchaseOrder->code }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $grn->received_date }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $grn->receiver->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-300">
                                        {{ $grn->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 italic">{{ __('No goods receipt notes logged.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t dark:border-gray-700">{{ $grnList->links() }}</div>

        @elseif($activeTab === 'invoices')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Invoice Code') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Supplier') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Vendor Inv No.') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Due Date') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Total') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        @forelse($invoiceList as $inv)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $inv->code }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $inv->supplier->name }}</td>
                                <td class="px-6 py-4 font-mono text-gray-500">{{ $inv->invoice_number }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $inv->due_date }}</td>
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">₹{{ number_format($inv->total_amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-300">
                                        {{ $inv->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 italic">{{ __('No purchase invoices logged.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t dark:border-gray-700">{{ $invoiceList->links() }}</div>
        @endif
    </div>

    <!-- Requisition form modal -->
    @if($showingReqModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="$set('showingReqModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border dark:border-gray-700">
                    <form wire:submit="saveRequisition">
                        <div class="px-6 py-4 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Create Requisition') }}</h3>
                        </div>

                        <div class="p-6 space-y-4 max-h-[500px] overflow-y-auto">
                            <div>
                                <x-input-label for="req-needed" :value="__('Needed By Date')" />
                                <x-text-input wire:model="reqNeededDate" id="req-needed" type="date" class="mt-1 block w-full" required />
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Line Items') }}</h4>
                                    <button type="button" wire:click="addReqItem" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">+ Add Line</button>
                                </div>

                                @foreach($reqItems as $index => $item)
                                    <div class="grid grid-cols-3 gap-2 items-center bg-gray-50 dark:bg-gray-900 p-3 rounded-lg border dark:border-gray-800">
                                        <select wire:model="reqItems.{{ $index }}.product_id" class="col-span-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" required>
                                            <option value="">-- {{ __('Select Product') }} --</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }} (₹{{ $p->purchase_price }})</option>
                                            @endforeach
                                        </select>
                                        <div class="flex items-center justify-between">
                                            <input type="number" wire:model="reqItems.{{ $index }}.quantity" class="w-20 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" placeholder="Qty" required />
                                            <button type="button" wire:click="removeReqItem({{ $index }})" class="text-red-500 hover:text-red-700">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div>
                                <x-input-label for="req-remarks" :value="__('Remarks')" />
                                <textarea wire:model="reqRemarks" id="req-remarks" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2"></textarea>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t dark:border-gray-700">
                            <x-primary-button type="submit">{{ __('Save Requisition') }}</x-primary-button>
                            <x-secondary-button wire:click="$set('showingReqModal', false)">{{ __('Cancel') }}</x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- PO form modal -->
    @if($showingPoModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="$set('showingPoModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border dark:border-gray-700">
                    <form wire:submit="savePurchaseOrder">
                        <div class="px-6 py-4 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Generate Purchase Order') }}</h3>
                        </div>

                        <div class="p-6 space-y-4 max-h-[500px] overflow-y-auto">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="po-supplier" :value="__('Supplier')" />
                                    <select wire:model="poSupplierId" id="po-supplier" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2" required>
                                        <option value="">-- {{ __('Select Supplier') }} --</option>
                                        @foreach($suppliers as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="po-date" :value="__('PO Date')" />
                                    <x-text-input wire:model="poDate" id="po-date" type="date" class="mt-1 block w-full" required />
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Line Items') }}</h4>
                                    <button type="button" wire:click="addPoItem" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">+ Add Line</button>
                                </div>

                                @foreach($poItems as $index => $item)
                                    <div class="grid grid-cols-4 gap-2 items-center bg-gray-50 dark:bg-gray-900 p-3 rounded-lg border dark:border-gray-800">
                                        <select wire:model="poItems.{{ $index }}.product_id" class="col-span-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" required>
                                            <option value="">-- {{ __('Select Product') }} --</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }} (₹{{ $p->purchase_price }})</option>
                                            @endforeach
                                        </select>
                                        <input type="number" wire:model="poItems.{{ $index }}.quantity" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" placeholder="Qty" required />
                                        <div class="flex items-center justify-between">
                                            <input type="number" step="0.01" wire:model="poItems.{{ $index }}.unit_price" class="w-20 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" placeholder="Cost" required />
                                            <button type="button" wire:click="removePoItem({{ $index }})" class="text-red-500 hover:text-red-700">
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
                            <x-primary-button type="submit">{{ __('Approve & Save') }}</x-primary-button>
                            <x-secondary-button wire:click="$set('showingPoModal', false)">{{ __('Cancel') }}</x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- GRN form modal -->
    @if($showingGrnModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="$set('showingGrnModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border dark:border-gray-700">
                    <form wire:submit="saveGRN">
                        <div class="px-6 py-4 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Log Goods Receipt Note (GRN)') }}</h3>
                        </div>

                        <div class="p-6 space-y-4 max-h-[500px] overflow-y-auto">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="grn-po" :value="__('Purchase Order Reference')" />
                                    <select wire:model="grnPoId" id="grn-po" wire:change="loadPoItems" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2" required>
                                        <option value="">-- {{ __('Select PO') }} --</option>
                                        @foreach($approvedPOs as $po)
                                            <option value="{{ $po->id }}">{{ $po->code }} ({{ $po->supplier->name }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="grn-date" :value="__('Received Date')" />
                                    <x-text-input wire:model="grnReceivedDate" id="grn-date" type="date" class="mt-1 block w-full" required />
                                </div>
                            </div>

                            @if(count($grnItems) > 0)
                                <div class="space-y-3">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Inspect Deliveries') }}</h4>
                                    @foreach($grnItems as $index => $item)
                                        <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border dark:border-gray-800 space-y-3">
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="font-bold text-gray-900 dark:text-white">{{ $item['product_name'] }}</span>
                                                <span class="text-xs text-gray-400">Ordered: {{ $item['quantity_ordered'] }}</span>
                                            </div>
                                            <div class="grid grid-cols-4 gap-2">
                                                <div>
                                                    <label class="block text-[10px] uppercase font-bold text-gray-400">{{ __('Recd') }}</label>
                                                    <input type="number" wire:model="grnItems.{{ $index }}.quantity_received" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1" required />
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] uppercase font-bold text-gray-400">{{ __('Accept') }}</label>
                                                    <input type="number" wire:model="grnItems.{{ $index }}.quantity_accepted" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1" required />
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] uppercase font-bold text-gray-400">{{ __('Batch Code') }}</label>
                                                    <input type="text" wire:model="grnItems.{{ $index }}.batch_number" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1" />
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] uppercase font-bold text-gray-400">{{ __('Expiry') }}</label>
                                                    <input type="date" wire:model="grnItems.{{ $index }}.expiry_date" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1" />
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t dark:border-gray-700">
                            <x-primary-button type="submit">{{ __('Log Stock Receipt') }}</x-primary-button>
                            <x-secondary-button wire:click="$set('showingGrnModal', false)">{{ __('Cancel') }}</x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Invoice form modal -->
    @if($showingInvModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="$set('showingInvModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border dark:border-gray-700">
                    <form wire:submit="saveInvoice">
                        <div class="px-6 py-4 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Record Purchase Invoice') }}</h3>
                        </div>

                        <div class="p-6 space-y-4 max-h-[500px] overflow-y-auto">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="inv-supplier" :value="__('Supplier')" />
                                    <select wire:model="invSupplierId" id="inv-supplier" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2" required>
                                        <option value="">-- {{ __('Select Supplier') }} --</option>
                                        @foreach($suppliers as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="inv-no" :value="__('Supplier Invoice Number')" />
                                    <x-text-input wire:model="invNumber" id="inv-no" class="mt-1 block w-full" placeholder="e.g. USL-10293" required />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="inv-date" :value="__('Invoice Date')" />
                                    <x-text-input wire:model="invDate" id="inv-date" type="date" class="mt-1 block w-full" required />
                                </div>
                                <div>
                                    <x-input-label for="inv-due" :value="__('Due Date')" />
                                    <x-text-input wire:model="invDueDate" id="inv-due" type="date" class="mt-1 block w-full" required />
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Line Items') }}</h4>
                                    <button type="button" wire:click="addInvItem" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">+ Add Line</button>
                                </div>

                                @foreach($invItems as $index => $item)
                                    <div class="grid grid-cols-4 gap-2 items-center bg-gray-50 dark:bg-gray-900 p-3 rounded-lg border dark:border-gray-800">
                                        <select wire:model="invItems.{{ $index }}.product_id" class="col-span-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" required>
                                            <option value="">-- {{ __('Select Product') }} --</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="number" wire:model="invItems.{{ $index }}.quantity" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" placeholder="Qty" required />
                                        <div class="flex items-center justify-between">
                                            <input type="number" step="0.01" wire:model="invItems.{{ $index }}.unit_price" class="w-20 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" placeholder="Price" required />
                                            <button type="button" wire:click="removeInvItem({{ $index }})" class="text-red-500 hover:text-red-700">
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
                            <x-primary-button type="submit">{{ __('Log Invoice') }}</x-primary-button>
                            <x-secondary-button wire:click="$set('showingInvModal', false)">{{ __('Cancel') }}</x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
