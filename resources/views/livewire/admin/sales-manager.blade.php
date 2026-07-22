<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ __('Sales Management Hub') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Orchestrate sales pipelines, invoices, returns, credit notes, and profitability metrics.') }}
            </p>
        </div>
        <div>
            <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('New Sales Order') }}
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('success'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-lg flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Tabs Nav -->
    <div class="border-b border-gray-200 dark:border-gray-700 flex space-x-8 overflow-x-auto">
        <button wire:click="setTab('pipeline')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'pipeline' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Orders Pipeline') }}
        </button>
        <button wire:click="setTab('invoices')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'invoices' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Sales Invoices') }}
        </button>
        <button wire:click="setTab('returns')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'returns' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Customer Returns & Credits') }}
        </button>
        <button wire:click="setTab('analytics')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'analytics' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Margin Analytics') }}
        </button>
    </div>

    <!-- Main Container -->
    <div class="space-y-6">
        @if ($activeTab === 'pipeline')
            <!-- Pipeline Controls -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex-1 max-w-md">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by Order #..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex items-center gap-2">
                    <select wire:model.live="orderType" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Channel Types</option>
                        <option value="walk_in">Walk-in Store</option>
                        <option value="online">Online App</option>
                        <option value="corporate">Corporate Order</option>
                        <option value="wholesale">Wholesale Order</option>
                    </select>

                    <select wire:model.live="status" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Order #</th>
                                <th class="px-6 py-3">Customer</th>
                                <th class="px-6 py-3">Warehouse</th>
                                <th class="px-6 py-3">Channel</th>
                                <th class="px-6 py-3 text-right">Total (Inc. Tax)</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors">
                                    <td class="px-6 py-4 font-mono text-sm text-indigo-600 dark:text-indigo-400 font-bold">{{ $order->order_number }}</td>
                                    <td class="px-6 py-4">
                                        @if ($order->customer)
                                            <div class="font-semibold text-gray-950 dark:text-white">{{ $order->customer->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->customer->phone }}</div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500 text-sm italic">General Retail (Walk-in)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $order->warehouse->name }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full 
                                            {{ $order->order_type === 'online' ? 'bg-sky-100 dark:bg-sky-950/40 text-sky-700 dark:text-sky-400' : '' }}
                                            {{ $order->order_type === 'corporate' ? 'bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400' : '' }}
                                            {{ $order->order_type === 'wholesale' ? 'bg-purple-100 dark:bg-purple-950/40 text-purple-700 dark:text-purple-400' : '' }}
                                            {{ $order->order_type === 'walk_in' ? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' : '' }}
                                        ">
                                            {{ ucfirst($order->order_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">₹{{ number_format($order->total_amount, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                            {{ $order->status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-950/40 text-yellow-700 dark:text-yellow-400' : '' }}
                                            {{ $order->status === 'processing' ? 'bg-indigo-100 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400' : '' }}
                                            {{ $order->status === 'shipped' ? 'bg-blue-100 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400' : '' }}
                                            {{ $order->status === 'delivered' ? 'bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-400' : '' }}
                                            {{ $order->status === 'cancelled' ? 'bg-red-100 dark:bg-red-950/40 text-red-700 dark:text-red-400' : '' }}
                                        ">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            @if ($order->status === 'pending')
                                                <button wire:click="updateOrderStatus('{{ $order->id }}', 'processing')" class="text-xs px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded cursor-pointer transition-colors">Start Process</button>
                                                <button wire:click="updateOrderStatus('{{ $order->id }}', 'cancelled')" class="text-xs px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded cursor-pointer transition-colors">Cancel</button>
                                            @elseif ($order->status === 'processing')
                                                <button wire:click="updateOrderStatus('{{ $order->id }}', 'shipped')" class="text-xs px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded cursor-pointer transition-colors">Ship Order</button>
                                            @elseif ($order->status === 'shipped')
                                                <button wire:click="updateOrderStatus('{{ $order->id }}', 'delivered')" class="text-xs px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded cursor-pointer transition-colors">Complete</button>
                                            @elseif ($order->status === 'delivered')
                                                @php
                                                    $returned = \App\Models\SalesReturn::where('sales_order_id', $order->id)->exists();
                                                @endphp
                                                @if(!$returned)
                                                    <button wire:click="openReturnModal('{{ $order->id }}')" class="text-xs px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded cursor-pointer transition-colors flex items-center gap-1">
                                                        Return Item
                                                    </button>
                                                @else
                                                    <span class="text-xs text-amber-500 italic font-medium">Returned</span>
                                                @endif
                                            @else
                                                <span class="text-gray-400 text-xs italic">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No sales orders found matching your criteria.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div>
                {{ $orders->links() }}
            </div>
        @endif

        @if ($activeTab === 'invoices')
            <!-- Invoice Controls -->
            <div class="flex bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex-1 max-w-md">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by Invoice #..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Invoice #</th>
                                <th class="px-6 py-3">Sales Order #</th>
                                <th class="px-6 py-3">Customer</th>
                                <th class="px-6 py-3 text-right">Invoiced Sum</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-center">Date Generated</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($invoices as $invoice)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors">
                                    <td class="px-6 py-4 font-mono text-sm text-indigo-600 dark:text-indigo-400 font-bold">{{ $invoice->invoice_number }}</td>
                                    <td class="px-6 py-4 font-mono text-sm text-gray-600 dark:text-gray-400">{{ $invoice->order->order_number }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @if ($invoice->order->customer)
                                            <div class="font-semibold text-gray-900 dark:text-white">{{ $invoice->order->customer->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $invoice->order->customer->phone }}</div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500 text-sm italic">General Retail (Walk-in)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">₹{{ number_format($invoice->total_amount, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-400">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ $invoice->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No invoices found matching search.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div>
                {{ $invoices->links() }}
            </div>
        @endif

        @if ($activeTab === 'returns')
            <!-- Returns Controls -->
            <div class="flex bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex-1 max-w-md">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by Return #..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Return #</th>
                                <th class="px-6 py-3">Orig Sales Order #</th>
                                <th class="px-6 py-3">Reason</th>
                                <th class="px-6 py-3 text-right">Refund Amount</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-center">Credit Note Issued</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($returns as $ret)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors">
                                    <td class="px-6 py-4 font-mono text-sm text-indigo-600 dark:text-indigo-400 font-bold">{{ $ret->return_number }}</td>
                                    <td class="px-6 py-4 font-mono text-sm text-gray-600 dark:text-gray-400">{{ $ret->order->order_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $ret->reason)) }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">₹{{ number_format($ret->refund_amount, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-400">
                                            {{ ucfirst($ret->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $creditNote = \App\Models\CreditNote::where('sales_return_id', $ret->id)->first();
                                        @endphp
                                        @if ($creditNote)
                                            <div class="font-mono text-xs font-bold text-indigo-650 dark:text-indigo-400">{{ $creditNote->note_number }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">₹{{ number_format($creditNote->amount, 2) }}</div>
                                        @else
                                            <span class="text-gray-400 text-xs italic">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No customer return records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div>
                {{ $returns->links() }}
            </div>
        @endif

        @if ($activeTab === 'analytics')
            <!-- Analytics Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Gross Revenue -->
                <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 relative overflow-hidden">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Gross Revenue</div>
                    <div class="text-2xl font-black text-gray-900 dark:text-white mt-2">₹{{ number_format($analytics['gross_sales'], 2) }}</div>
                    <div class="w-1 h-full bg-indigo-600 absolute left-0 top-0"></div>
                </div>

                <!-- Net Returns -->
                <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 relative overflow-hidden">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Net Returns</div>
                    <div class="text-2xl font-black text-red-600 dark:text-red-400 mt-2">₹{{ number_format($analytics['net_returns'], 2) }}</div>
                    <div class="w-1 h-full bg-red-500 absolute left-0 top-0"></div>
                </div>

                <!-- COGS -->
                <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 relative overflow-hidden">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cost of Goods (COGS)</div>
                    <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-2">₹{{ number_format($analytics['cogs'], 2) }}</div>
                    <div class="w-1 h-full bg-amber-500 absolute left-0 top-0"></div>
                </div>

                <!-- Profit Margin -->
                <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 relative overflow-hidden">
                    <div class="flex justify-between items-center text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Profit Margin
                        <span class="text-green-600 dark:text-green-400 font-black text-sm">{{ number_format($analytics['gross_margin_percentage'], 2) }}%</span>
                    </div>
                    <div class="text-2xl font-black text-green-600 dark:text-green-400 mt-2">₹{{ number_format($analytics['gross_margin_amount'], 2) }}</div>
                    <div class="w-1 h-full bg-green-500 absolute left-0 top-0"></div>
                </div>
            </div>

            <!-- Summary Info -->
            <div class="p-4 bg-indigo-50 dark:bg-indigo-950/20 border-l-4 border-indigo-500 text-sm rounded-r-lg text-indigo-900 dark:text-indigo-300">
                <h4 class="font-bold text-indigo-950 dark:text-white mb-1">How profitability margin is computed:</h4>
                Gross profit margins are computed dynamically. Every item checkout subtracts its unit cost balance (computed from the product purchasing price register), adjusts dynamically based on any customer returns, and divides the resulting net profit margin by total incoming revenue.
            </div>
        @endif
    </div>

    <!-- CREATE ORDER MODAL -->
    @if ($showingCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-3xl rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200 flex flex-col max-h-[90vh]">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Create New Sales Order</h3>
                    <button wire:click="$set('showingCreateModal', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-6 overflow-y-auto flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Order Channel Type -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Order Channel</label>
                            <select wire:model.live="newOrderType" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                                <option value="walk_in">Walk-in Store</option>
                                <option value="online">Online App</option>
                                <option value="corporate">Corporate Order</option>
                                <option value="wholesale">Wholesale Order</option>
                            </select>
                        </div>

                        <!-- Source Warehouse -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Source Warehouse</label>
                            <select wire:model="newWarehouseId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                                @foreach ($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Customer Search (Only if not standard walk_in) -->
                    @if ($newOrderType !== 'walk_in')
                        <div class="p-4 bg-gray-50 dark:bg-gray-850/50 border border-gray-200 dark:border-gray-800 rounded-lg">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Assign Customer Account</label>
                            <input type="text" wire:model.live.debounce.300ms="customerSearch" placeholder="Search customer by name or phone..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            
                            @if ($customers->isNotEmpty())
                                <div class="mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($customers as $c)
                                        <button type="button" wire:click="$set('newCustomerId', '{{ $c->id }}'); $set('customerSearch', '{{ $c->name }}')" class="w-full text-left px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-950/20 text-sm flex justify-between items-center transition-colors">
                                            <div>
                                                <span class="text-gray-900 dark:text-white font-semibold">{{ $c->name }}</span>
                                                <span class="text-gray-500 dark:text-gray-400 text-xs ml-2">({{ $c->phone }})</span>
                                            </div>
                                            @if ($newCustomerId === $c->id)
                                                <span class="text-xs bg-indigo-600 text-white font-bold px-2 py-0.5 rounded-full">Selected</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Delivery Address -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Delivery Address</label>
                            <textarea wire:model="newDeliveryAddress" rows="2" placeholder="Enter delivery address..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-2 px-3"></textarea>
                        </div>
                    @endif

                    <!-- Items List -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Order Items Checkout List</label>
                            <button type="button" wire:click="addOrderItem" class="text-xs px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 font-bold rounded cursor-pointer transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Item
                            </button>
                        </div>

                        <div class="space-y-3">
                            @foreach ($newOrderItems as $idx => $orderItem)
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 bg-gray-50 dark:bg-gray-850/40 p-3 rounded-lg border border-gray-200 dark:border-gray-800 relative items-center">
                                    <!-- Select Product -->
                                    <div class="md:col-span-2">
                                        <select wire:model="newOrderItems.{{ $idx }}.product_id" wire:change="updateItemPrice({{ $idx }})" class="w-full rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-1.5 px-3">
                                            @foreach ($products as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->volume_ml }}ml) - ₹{{ $p->selling_price }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Quantity -->
                                    <div>
                                        <input type="number" step="1" min="1" wire:model.live="newOrderItems.{{ $idx }}.quantity" placeholder="Qty" class="w-full rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-1.5 px-3 text-center">
                                    </div>

                                    <!-- Price / Remove -->
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="font-mono font-bold text-gray-900 dark:text-white text-sm">
                                            ₹{{ number_format(floatval($orderItem['quantity'] ?? 0) * floatval($orderItem['unit_price'] ?? 0), 2) }}
                                        </div>
                                        <button type="button" wire:click="removeOrderItem({{ $idx }})" class="text-red-600 hover:text-red-500 p-1 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-between items-center">
                    <div>
                        @php
                            $sub = collect($newOrderItems)->sum(fn($i) => floatval($i['quantity'] ?? 0) * floatval($i['unit_price'] ?? 0));
                            $total = $sub * 1.18;
                        @endphp
                        <span class="text-gray-500 dark:text-gray-400 text-xs block uppercase font-bold">Total Invoice Amount (Inc. 18% GST)</span>
                        <span class="text-xl font-black text-gray-900 dark:text-white font-mono">₹{{ number_format($total, 2) }}</span>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" wire:click="$set('showingCreateModal', false)" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 text-gray-700 dark:text-gray-300 font-semibold rounded-lg cursor-pointer transition-colors">Cancel</button>
                        <button type="button" wire:click="saveOrder" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg cursor-pointer shadow transition-colors">Submit Order</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- RETURN ITEMS MODAL -->
    @if ($showingReturnModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-2xl rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Process Customer Return</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">Order: {{ $selectedOrderForReturn?->order_number }}</p>
                    </div>
                    <button wire:click="$set('showingReturnModal', false)" class="text-gray-400 hover:text-gray-650 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-6">
                    <!-- Reason -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Reason for Return</label>
                        <select wire:model="returnReason" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2.5 px-3">
                            <option value="wrong_item">Wrong Item Delivered</option>
                            <option value="damaged">Damaged in Transit / Defective bottle</option>
                            <option value="expired">Expired Stock</option>
                        </select>
                    </div>

                    <!-- Items Select -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Returned Quantity Config</label>
                        <div class="space-y-3">
                            @foreach ($returnItems as $index => $item)
                                <div class="flex items-center justify-between p-3.5 bg-gray-50 dark:bg-gray-850/40 border border-gray-200 dark:border-gray-800 rounded-lg gap-4">
                                    <div class="flex-1">
                                        <div class="text-gray-900 dark:text-white font-semibold text-sm">{{ $item['product_name'] }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Purchased: {{ $item['purchased_qty'] }} @ ₹{{ number_format($item['refund_unit_price'], 2) }}</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="text-gray-500 dark:text-gray-400 text-xs">Return Qty:</label>
                                        <input type="number" min="0" max="{{ $item['purchased_qty'] }}" step="1" wire:model.live="returnItems.{{ $index }}.quantity_to_return" wire:change="calculateRefundAmount" class="w-16 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded text-center py-1 text-gray-950 dark:text-white font-bold text-sm focus:outline-none focus:border-indigo-500">
                                    </div>
                                </div>
                            @endforeach
                            @error('returnItems')
                                <p class="text-xs text-red-650 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-between items-center">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 text-xs block uppercase font-bold">Credited Amount (Inc. 18% GST Refund)</span>
                        <span class="text-xl font-black text-green-600 dark:text-green-400 font-mono">₹{{ number_format($refundAmount, 2) }}</span>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" wire:click="$set('showingReturnModal', false)" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 text-gray-700 dark:text-gray-300 font-semibold rounded-lg cursor-pointer transition-colors">Cancel</button>
                        <button type="button" wire:click="saveReturn" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg cursor-pointer shadow transition-colors">Approve Return</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
