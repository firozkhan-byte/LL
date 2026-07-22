<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            {{ __('AI & Business Intelligence Control Room') }}
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ __('Machine learning demand forecasts, sales projections, intelligent purchase suggestions, and natural language AI assistant.') }}
        </p>
    </div>

    <!-- Tabs Nav -->
    <div class="border-b border-gray-200 dark:border-gray-700 flex space-x-8 overflow-x-auto">
        <button wire:click="setTab('dashboard')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'dashboard' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('CEO Executive Dashboard') }}
        </button>
        <button wire:click="setTab('forecast')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'forecast' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('AI Forecast Center') }}
        </button>
        <button wire:click="setTab('chat')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'chat' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('AI Assistant Terminal') }}
        </button>
    </div>

    <!-- Tab Contents -->
    <div class="space-y-6">
        @if ($activeTab === 'dashboard')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- CEO KPI scorecards -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-950 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">Strategic Targets</h3>
                    <div class="space-y-3 text-sm">
                        <div class="p-3 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Current Sales Target</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white mt-1 block">₹500,000.00</span>
                        </div>
                        <div class="p-3 bg-indigo-50 dark:bg-indigo-950/30 rounded-lg">
                            <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block">ML Projected Sales</span>
                            <span class="text-xl font-bold text-indigo-755 dark:text-indigo-300 mt-1 block font-mono">₹{{ number_format($forecast['projected_next_month_sales'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Customer segmentations widget -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-950 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">Customer Segmentation</h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center py-1">
                            <span class="text-gray-500 font-bold uppercase tracking-wider">Regular Customers</span>
                            <span class="font-mono text-sm font-black text-gray-900 dark:text-white bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded">{{ $segments['regular'] }} Clients</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-gray-500 font-bold uppercase tracking-wider">Premium Customers</span>
                            <span class="font-mono text-sm font-black text-indigo-600 bg-indigo-100 dark:bg-indigo-950 px-2 py-0.5 rounded">{{ $segments['premium'] }} VIPs</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-gray-500 font-bold uppercase tracking-wider">Corporate Partners</span>
                            <span class="font-mono text-sm font-black text-green-700 bg-green-100 dark:bg-green-950 px-2 py-0.5 rounded">{{ $segments['corporate'] }} Partners</span>
                        </div>
                    </div>
                </div>

                <!-- Branch ranking performances -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-950 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">Branch Performances</h3>
                    <div class="divide-y divide-gray-150 dark:divide-gray-700">
                        @forelse ($branchPerformances as $perform)
                            <div class="py-2.5 flex justify-between items-center text-sm">
                                <div>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $perform['name'] }}</span>
                                    <span class="text-xs text-gray-400 block font-mono">Code: {{ $perform['code'] }}</span>
                                </div>
                                <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">₹{{ number_format($perform['sales_sum'], 2) }}</span>
                            </div>
                        @empty
                            <span class="text-gray-400 italic text-xs">No active branch data compiled.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        @if ($activeTab === 'forecast')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Projections Card -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-950 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">Predictive Sales Analytics</h3>
                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-bold uppercase tracking-wider">Current Month Sales:</span>
                            <span class="font-mono font-bold text-gray-900 dark:text-white">₹{{ number_format($forecast['current_month_sales'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider">ML Projected Next Month:</span>
                            <span class="font-mono font-black text-indigo-755 dark:text-indigo-300">₹{{ number_format($forecast['projected_next_month_sales'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center border-t border-gray-100 dark:border-gray-700 pt-3">
                            <span class="text-gray-500 font-bold">Growth Trend Vector:</span>
                            <span class="font-mono font-bold text-green-600">+{{ $forecast['growth_rate_projected'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-bold">Model Confidence Level:</span>
                            <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $forecast['confidence_level_percentage'] }}% Confidence</span>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Replenishment Suggestions -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-950 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">Restock Suggestions</h3>
                    <div class="divide-y divide-gray-150 dark:divide-gray-700">
                        @forelse ($suggestions as $suggest)
                            <div class="py-3 flex justify-between items-center text-sm">
                                <div>
                                    <span class="font-bold text-gray-900 dark:text-white block">{{ $suggest['product_name'] }}</span>
                                    <span class="text-xs text-gray-400 block font-mono">Current Stock: {{ $suggest['current_stock'] }} Bottles | Order Suggestion: {{ $suggest['recommended_order_quantity'] }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="block font-mono text-indigo-600 font-bold">₹{{ number_format($suggest['estimated_replenish_cost'], 2) }}</span>
                                    <span class="text-xs text-gray-400 block">Est. Cost</span>
                                </div>
                            </div>
                        @empty
                            <span class="text-gray-400 italic text-xs py-4 block">All active SKU inventory counts are within standard safety thresholds.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        @if ($activeTab === 'chat')
            <!-- AI Chat Console Terminal -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col h-[500px]">
                <!-- Chat Log Viewport -->
                <div class="flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50/50 dark:bg-slate-900/10">
                    @foreach ($chatHistory as $msg)
                        <div class="flex {{ $msg['sender'] === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xl rounded-xl px-4 py-2.5 text-sm shadow-sm
                                {{ $msg['sender'] === 'user'
                                    ? 'bg-indigo-600 text-white rounded-br-none'
                                    : 'bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-650 text-gray-900 dark:text-white rounded-bl-none'
                                }}">
                                <p class="leading-relaxed font-semibold">{{ $msg['message'] }}</p>
                                <span class="block text-right text-[10px] text-gray-300 dark:text-slate-400 mt-1 select-none font-mono">{{ $msg['timestamp'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Chat Input Controls bar -->
                <div class="p-4 border-t border-gray-250 dark:border-gray-700 bg-white dark:bg-gray-800 flex gap-3">
                    <input type="text" wire:model="chatInput" wire:keydown.enter="sendChatMessage" placeholder="Ask AI: 'Show sales report' or 'Predicted sales forecasts'..." class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-950 dark:text-gray-100 text-sm py-2.5 px-3">
                    <button wire:click="sendChatMessage" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-sm shadow cursor-pointer transition">
                        Send Question
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
