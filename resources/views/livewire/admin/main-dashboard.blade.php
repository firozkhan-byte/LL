<div class="space-y-6">
    <!-- Stat grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Sales revenue -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Sales Revenue</span>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1 font-mono">₹{{ number_format($salesRevenue, 2) }}</h3>
                <span class="text-xs text-gray-400 block mt-1">{{ $pendingSales }} processing orders</span>
            </div>
            <div class="p-3 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-650 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Inventory bottles count -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Current Stock</span>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1 font-mono">{{ number_format($totalStockCount) }} Bottles</h3>
                <span class="text-xs text-gray-400 block mt-1">Valuation compiled in reports</span>
            </div>
            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-650 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
        </div>

        <!-- Attendance Rate -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Daily Attendance</span>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1 font-mono">{{ $attendanceRate }}%</h3>
                <span class="text-xs text-gray-400 block mt-1">{{ $staffCount }} active employee profiles</span>
            </div>
            <div class="p-3 bg-blue-50 dark:bg-blue-950/40 text-blue-650 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>

        <!-- Compliance licenses alert -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">License Status</span>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1 font-mono">
                    @if ($expiringLicenses > 0)
                        <span class="text-red-650">{{ $expiringLicenses }} Expiring</span>
                    @else
                        <span class="text-green-700">All Active</span>
                    @endif
                </h3>
                <span class="text-xs text-gray-400 block mt-1">Excise validation up-to-date</span>
            </div>
            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 text-amber-650 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Active work layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Orders table -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-4">
            <h3 class="text-base font-bold text-gray-950 dark:text-white pb-3 border-b border-gray-150 dark:border-gray-700">Recent Enterprise Sales Orders</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            <th class="py-2">Order #</th>
                            <th class="py-2">Customer</th>
                            <th class="py-2 text-right">Order value</th>
                            <th class="py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($recentOrders as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/25 transition">
                                <td class="py-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $order->order_number }}</td>
                                <td class="py-3 font-bold text-gray-900 dark:text-white">{{ $order->customer->name }}</td>
                                <td class="py-3 text-right font-mono font-bold">₹{{ number_format($order->total_amount, 2) }}</td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-0.5 text-xs font-bold uppercase rounded-full bg-indigo-50 text-indigo-700 dark:text-indigo-400">
                                        {{ $order->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500 italic">No orders logged in current sequence.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Transit deliveries queue + CRM support ticket alert -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 space-y-6">
            <div>
                <h3 class="text-base font-bold text-gray-950 dark:text-white pb-3 border-b border-gray-150 dark:border-gray-700 mb-3">Transit Deliveries status</h3>
                <div class="space-y-3">
                    @forelse ($activeDeliveries as $del)
                        <div class="flex justify-between items-center text-xs p-2 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                            <div>
                                <span class="font-bold text-indigo-650 block">{{ $del->salesOrder->order_number }}</span>
                                <span class="text-[10px] text-gray-400">Driver: {{ $del->agent->name }}</span>
                            </div>
                            <span class="px-2 py-0.5 font-bold uppercase rounded bg-blue-105 text-blue-700 dark:text-blue-400">
                                {{ $del->status }}
                            </span>
                        </div>
                    @empty
                        <span class="text-gray-500 italic text-xs block py-4 text-center">No active deliveries currently on route.</span>
                    @endforelse
                </div>
            </div>

            <!-- CRM Tickets -->
            <div class="p-4 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900 rounded-lg flex items-center justify-between">
                <div>
                    <span class="text-sm font-bold text-amber-800 dark:text-amber-300">Helpdesk Support Tickets</span>
                    <span class="block text-xs text-amber-600 mt-1">{{ $pendingTickets }} pending open queries requiring feedback.</span>
                </div>
                <span class="h-8 w-8 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold text-sm">{{ $pendingTickets }}</span>
            </div>
        </div>
    </div>

    <!-- AI Mini Assistant chatbot widget -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="bg-indigo-600 dark:bg-indigo-950/40 p-4 flex justify-between items-center text-white">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="font-bold text-sm">ERP Interactive AI Assistant Console</span>
            </div>
            <span class="text-[10px] bg-indigo-500 px-2 py-0.5 rounded uppercase font-bold">Online</span>
        </div>
        <div class="p-4 space-y-4">
            <!-- Messages timeline -->
            <div class="space-y-2 max-h-[160px] overflow-y-auto text-xs">
                @foreach ($chatHistory as $msg)
                    <div class="flex {{ $msg['sender'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-md rounded-lg p-2 {{ $msg['sender'] === 'user' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' }} font-semibold">
                            {{ $msg['message'] }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Input prompt -->
            <div class="flex gap-2">
                <input type="text" wire:model="chatInput" wire:keydown.enter="askAI" placeholder="Ask AI: 'Show monthly sales', 'What is forecasted next month?', or 'Any inventory items low?'..." class="flex-1 rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-950 dark:text-gray-100 text-xs py-1.5 px-3">
                <button wire:click="askAI" class="bg-indigo-650 hover:bg-indigo-755 text-white font-bold text-xs px-3 rounded cursor-pointer transition">
                    Ask
                </button>
            </div>
        </div>
    </div>
</div>
