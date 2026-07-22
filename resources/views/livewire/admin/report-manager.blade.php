<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ __('Enterprise Reports & Analytics Dashboard') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Unified operational summaries of Sales, Purchases, Warehouses, GST filings, general ledger journals, and roster rosters.') }}
            </p>
        </div>
        <div>
            <button wire:click="exportCSV" class="inline-flex items-center px-4 py-2 bg-indigo-650 hover:bg-indigo-755 text-white rounded-lg text-sm font-semibold shadow cursor-pointer transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                {{ __('Export CSV Report') }}
            </button>
        </div>
    </div>

    <!-- Alert -->
    @if (session()->has('report_success'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-lg flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('report_success') }}</span>
        </div>
    @endif

    <!-- Date range picker bar -->
    <div class="bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col md:flex-row items-end md:items-center gap-4">
        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Report Start Date</label>
                <input type="date" wire:model.live="startDate" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Report End Date</label>
                <input type="date" wire:model.live="endDate" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
            </div>
        </div>
    </div>

    <!-- Analytics Dashboard widgets -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Total Sales Turnover</span>
            <h3 class="text-3xl font-black text-indigo-650 dark:text-indigo-400 mt-2 font-mono">₹{{ number_format($report['sales']['total'], 2) }}</h3>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Total Purchase Outlay</span>
            <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-2 font-mono">₹{{ number_format($report['purchase']['total'], 2) }}</h3>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Inventory Valuation</span>
            <h3 class="text-3xl font-black text-green-600 mt-2 font-mono">₹{{ number_format($report['inventory']['valuation'], 2) }}</h3>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Net GST Payable</span>
            <h3 class="text-3xl font-black text-amber-600 mt-2 font-mono">₹{{ number_format($report['compliance']['net_gst'], 2) }}</h3>
        </div>
    </div>

    <!-- Tabs Nav -->
    <div class="border-b border-gray-200 dark:border-gray-700 flex space-x-8 overflow-x-auto">
        <button wire:click="setTab('sales')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'sales' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Sales Analytics') }}
        </button>
        <button wire:click="setTab('purchase')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'purchase' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Purchasing') }}
        </button>
        <button wire:click="setTab('inventory')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'inventory' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Inventory Valuation') }}
        </button>
        <button wire:click="setTab('finance')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'finance' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('General Ledger') }}
        </button>
        <button wire:click="setTab('compliance')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'compliance' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('GST & Excise') }}
        </button>
        <button wire:click="setTab('rosters')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'rosters' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('HR & Directory') }}
        </button>
    </div>

    <!-- Tab Contents -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-6">
        @if ($activeTab === 'sales')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                <div class="p-4 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Sales Orders Placed</span>
                    <span class="text-2xl font-black text-gray-900 dark:text-white mt-1 block font-mono">{{ $report['sales']['count'] }} Orders</span>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total Sales Value</span>
                    <span class="text-2xl font-black text-gray-900 dark:text-white mt-1 block font-mono">₹{{ number_format($report['sales']['total'], 2) }}</span>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Average Order Value (AOV)</span>
                    <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1 block font-mono">₹{{ number_format($report['sales']['aov'], 2) }}</span>
                </div>
            </div>
        @endif

        @if ($activeTab === 'purchase')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div class="p-4 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Purchase Receipts Logged</span>
                    <span class="text-2xl font-black text-gray-900 dark:text-white mt-1 block font-mono">{{ $report['purchase']['count'] }} POs</span>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total Vendor Expenditures</span>
                    <span class="text-2xl font-black text-gray-900 dark:text-white mt-1 block font-mono">₹{{ number_format($report['purchase']['total'], 2) }}</span>
                </div>
            </div>
        @endif

        @if ($activeTab === 'inventory')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                <div class="p-4 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Liquor Items In Stock</span>
                    <span class="text-2xl font-black text-gray-900 dark:text-white mt-1 block font-mono">{{ number_format($report['inventory']['total_items']) }} Bottles</span>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Catalog Product count</span>
                    <span class="text-2xl font-black text-gray-900 dark:text-white mt-1 block font-mono">{{ $report['inventory']['products_count'] }} SKUs</span>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Estimated Stock Valuation</span>
                    <span class="text-2xl font-black text-green-600 mt-1 block font-mono">₹{{ number_format($report['inventory']['valuation'], 2) }}</span>
                </div>
            </div>
        @endif

        @if ($activeTab === 'finance')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div class="p-4 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Debit Ledger Balance Sum</span>
                    <span class="text-2xl font-black text-gray-900 dark:text-white mt-1 block font-mono">₹{{ number_format($report['finance']['debits'], 2) }}</span>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Credit Ledger Balance Sum</span>
                    <span class="text-2xl font-black text-gray-900 dark:text-white mt-1 block font-mono">₹{{ number_format($report['finance']['credits'], 2) }}</span>
                </div>
            </div>
        @endif

        @if ($activeTab === 'compliance')
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-sm">
                <div class="p-4 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Output GST (Collected)</span>
                    <span class="text-2xl font-black text-gray-900 dark:text-white mt-1 block font-mono">₹{{ number_format($report['compliance']['output_gst'], 2) }}</span>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Input GST (Credits)</span>
                    <span class="text-2xl font-black text-gray-900 dark:text-white mt-1 block font-mono">₹{{ number_format($report['compliance']['input_gst'], 2) }}</span>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Net GST Tax Payable</span>
                    <span class="text-2xl font-black text-amber-600 mt-1 block font-mono">₹{{ number_format($report['compliance']['net_gst'], 2) }}</span>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Active Licenses</span>
                    <span class="text-2xl font-black text-gray-900 dark:text-white mt-1 block font-mono">{{ $report['compliance']['active_licenses'] }} Licenses</span>
                </div>
            </div>
        @endif

        @if ($activeTab === 'rosters')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div class="p-4 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Active Roster Headcount</span>
                    <span class="text-2xl font-black text-gray-900 dark:text-white mt-1 block font-mono">{{ $report['hr']['headcount'] }} Employees</span>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-750/30 rounded-lg">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Daily Attendance Rate</span>
                    <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1 block font-mono">{{ $report['hr']['attendance_rate'] }}%</span>
                </div>
            </div>
        @endif
    </div>

    <!-- General Stats Details -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">Enterprise Directories</h4>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-500">Registered Customers</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $report['customers']['total'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Registered Suppliers</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $report['suppliers']['total'] }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">Infrastructure Assets</h4>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Branches</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $report['branches']['total'] }} Branches</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Warehouses</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $report['warehouses']['total'] }} Warehouses</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">Client Engagement</h4>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-500">New Customers onboarded</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $report['customers']['new'] }} signups</span>
                </div>
            </div>
        </div>
    </div>
</div>
