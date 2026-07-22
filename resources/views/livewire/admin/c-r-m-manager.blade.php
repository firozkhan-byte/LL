<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ __('Customer CRM & Support Desk') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Manage loyalty profiles, wallet transactions, bulk marketing campaigns, and customer support tickets.') }}
            </p>
        </div>
        <div>
            <button wire:click="openCreateTicketModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Open Support Ticket') }}
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
        <button wire:click="setTab('database')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'database' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Customer Profile Database') }}
        </button>
        <button wire:click="setTab('tickets')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'tickets' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Helpdesk Tickets') }}
        </button>
        <button wire:click="setTab('campaigns')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'campaigns' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Marketing Campaigns') }}
        </button>
        <button wire:click="setTab('analytics')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'analytics' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('CRM Metrics & Reports') }}
        </button>
    </div>

    <!-- Main Container -->
    <div class="space-y-6">
        @if ($activeTab === 'database')
            <!-- Search Controls -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex-1 max-w-md">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, phone..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Customer Name</th>
                                <th class="px-6 py-3">Phone</th>
                                <th class="px-6 py-3">Membership Tier</th>
                                <th class="px-6 py-3 text-right">Loyalty Points</th>
                                <th class="px-6 py-3 text-center">Wallet Balance</th>
                                <th class="px-6 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($customersList as $cust)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-950 dark:text-white">{{ $cust->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $cust->phone }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full uppercase
                                            {{ $cust->membership_type === 'vip' ? 'bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400' : '' }}
                                            {{ $cust->membership_type === 'gold' ? 'bg-yellow-100 dark:bg-yellow-950/40 text-yellow-700 dark:text-yellow-400' : '' }}
                                            {{ $cust->membership_type === 'silver' ? 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300' : '' }}
                                            {{ $cust->membership_type === 'regular' ? 'bg-blue-100 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400' : '' }}
                                        ">
                                            {{ $cust->membership_type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono font-bold text-gray-900 dark:text-white">{{ $cust->loyalty_points }} pts</td>
                                    <td class="px-6 py-4 text-center font-mono font-bold text-green-600 dark:text-green-400">
                                        ₹{{ number_format($cust->wallet ? $cust->wallet->balance : 0.00, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button wire:click="viewCustomerProfile('{{ $cust->id }}')" class="text-xs px-2.5 py-1 bg-indigo-650 hover:bg-indigo-750 text-white rounded cursor-pointer transition-colors">
                                            View & Adjust
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No customers profiles found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div>
                {{ $customersList->links() }}
            </div>
        @endif

        @if ($activeTab === 'tickets')
            <!-- Ticket Controls -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex-1 max-w-md">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search ticket subject..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                </div>
                <div class="flex items-center gap-2">
                    <select wire:model.live="ticketType" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        <option value="">All Ticket Types</option>
                        <option value="support">General Support</option>
                        <option value="complaint">Complaint</option>
                        <option value="feedback">Customer Feedback</option>
                    </select>

                    <select wire:model.live="ticketStatus" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        <option value="">All Statuses</option>
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Customer</th>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3">Subject</th>
                                <th class="px-6 py-3 text-center">Priority</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-center">Assigned Representative</th>
                                <th class="px-6 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($ticketsList as $ticket)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $ticket->customer->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->customer->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full uppercase
                                            {{ $ticket->type === 'complaint' ? 'bg-red-100 dark:bg-red-950/40 text-red-700 dark:text-red-400' : '' }}
                                            {{ $ticket->type === 'feedback' ? 'bg-blue-100 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400' : '' }}
                                            {{ $ticket->type === 'support' ? 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300' : '' }}
                                        ">
                                            {{ $ticket->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white max-w-xs truncate" title="{{ $ticket->description }}">
                                        <span class="font-semibold">{{ $ticket->subject }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 text-xs font-bold uppercase
                                            {{ $ticket->priority === 'high' ? 'text-red-650' : '' }}
                                            {{ $ticket->priority === 'medium' ? 'text-amber-600' : '' }}
                                            {{ $ticket->priority === 'low' ? 'text-slate-500' : '' }}
                                        ">
                                            {{ $ticket->priority }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                            {{ $ticket->status === 'open' ? 'bg-yellow-100 dark:bg-yellow-950/40 text-yellow-700 dark:text-yellow-400' : '' }}
                                            {{ $ticket->status === 'in_progress' ? 'bg-blue-100 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400' : '' }}
                                            {{ $ticket->status === 'resolved' ? 'bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-400' : '' }}
                                        ">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-700 dark:text-gray-300">
                                        {{ $ticket->assignee ? $ticket->assignee->name : 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($ticket->status !== 'resolved')
                                            <button wire:click="openTicketModal('{{ $ticket->id }}')" class="text-xs px-2 py-1 bg-indigo-650 hover:bg-indigo-750 text-white rounded cursor-pointer transition-colors">
                                                Update Status
                                            </button>
                                        @else
                                            <span class="text-xs text-green-600 dark:text-green-400 font-bold italic">Resolved</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No tickets found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div>
                {{ $ticketsList->links() }}
            </div>
        @endif

        @if ($activeTab === 'campaigns')
            <!-- Campaign Setup Form -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Send Campaign Form -->
                <div class="lg:col-span-1 bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Create SMS/Email Campaign</h3>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Campaign Name</label>
                        <input type="text" wire:model="campaignName" placeholder="e.g. Diwali Spirits Promotion" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Dispatch Channel</label>
                        <select wire:model.live="campaignChannel" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            <option value="email">Bulk Email Campaign</option>
                            <option value="sms">Bulk SMS Texting</option>
                            <option value="whatsapp">Bulk WhatsApp Message</option>
                        </select>
                    </div>

                    @if ($campaignChannel === 'email')
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Email Subject Line</label>
                            <input type="text" wire:model="campaignSubject" placeholder="Enter subject..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Campaign Message Content</label>
                        <textarea wire:model="campaignContent" rows="4" placeholder="Enter message payload text templates here..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm py-2 px-3"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Target Customer Count</label>
                        <input type="number" wire:model="campaignRecipientsCount" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 text-center">
                    </div>

                    <button type="button" wire:click="sendCampaign" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Dispatch Bulk Campaign
                    </button>
                </div>

                <!-- Past Campaigns Dispatch Log -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Past Dispatched Marketing Logs</h3>
                    <div class="space-y-4">
                        @forelse ($campaignsList as $camp)
                            <div class="p-4 bg-gray-50 dark:bg-gray-850/40 border border-gray-200 dark:border-gray-800 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-bold text-gray-950 dark:text-white">{{ $camp->name }}</span>
                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-400">
                                        {{ $camp->channel }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 italic mb-2">"{{ $camp->content }}"</p>
                                <div class="flex justify-between text-[11px] text-gray-500">
                                    <span>Sent to: <strong class="text-gray-750 dark:text-gray-300">{{ $camp->sent_count }} members</strong></span>
                                    <span>{{ $camp->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-sm text-center py-8">No marketing logs on record.</p>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $campaignsList->links() }}
                    </div>
                </div>
            </div>
        @endif

        @if ($activeTab === 'analytics')
            <!-- CRM Analytics Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Wallets Liabilities -->
                <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 relative overflow-hidden">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Wallets Liability</div>
                    <div class="text-2xl font-black text-gray-900 dark:text-white mt-2">₹{{ number_format($analytics['total_liability'], 2) }}</div>
                    <div class="w-1 h-full bg-red-500 absolute left-0 top-0"></div>
                </div>

                <!-- Open Tickets -->
                <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 relative overflow-hidden">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Open Tickets</div>
                    <div class="text-2xl font-black text-yellow-600 dark:text-yellow-400 mt-2">{{ $analytics['open_tickets'] }}</div>
                    <div class="w-1 h-full bg-yellow-500 absolute left-0 top-0"></div>
                </div>

                <!-- Tickets In-Progress -->
                <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 relative overflow-hidden">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tickets In-Progress</div>
                    <div class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-2">{{ $analytics['in_progress_tickets'] }}</div>
                    <div class="w-1 h-full bg-blue-500 absolute left-0 top-0"></div>
                </div>

                <!-- Today Birthdays -->
                <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 relative overflow-hidden">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Anniversary/Birthdays Today</div>
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2">{{ $analytics['today_birthday_count'] }}</div>
                    <div class="w-1 h-full bg-emerald-500 absolute left-0 top-0"></div>
                </div>
            </div>

            <!-- Campaign info -->
            <div class="p-4 bg-indigo-50 dark:bg-indigo-950/20 border-l-4 border-indigo-500 text-sm rounded-r-lg text-indigo-900 dark:text-indigo-300">
                <h4 class="font-bold text-indigo-950 dark:text-white mb-1">CRM Liability Details:</h4>
                Total Wallet Liability measures the current outstanding store credits/gift card values deposited or refunded back into customer accounts that can be redeemed during checkout billing cycles.
            </div>
        @endif
    </div>

    <!-- PROFILE DETAILS & WALLET ADJUSTMENT MODAL -->
    @if ($showingProfileModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-2xl rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $selectedCustomer?->name }} Profile Details</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Membership Tier: <strong class="uppercase text-indigo-600 dark:text-indigo-400">{{ $selectedCustomer?->membership_type }}</strong></p>
                    </div>
                    <button wire:click="$set('showingProfileModal', false)" class="text-gray-400 hover:text-gray-650 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-6">
                    <!-- Profile Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm bg-gray-50 dark:bg-gray-850/30 p-4 border border-gray-100 dark:border-gray-800 rounded-lg">
                        <div>
                            <span class="text-gray-500 block">Phone Number</span>
                            <span class="text-gray-900 dark:text-white font-semibold">{{ $selectedCustomer?->phone }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Loyalty Balance</span>
                            <span class="text-gray-900 dark:text-white font-semibold">{{ $selectedCustomer?->loyalty_points }} points</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Birthday Date</span>
                            <span class="text-gray-900 dark:text-white font-semibold">{{ $selectedProfile?->birthday ? $selectedProfile->birthday->format('M d, Y') : 'Not Configured' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Anniversary Date</span>
                            <span class="text-gray-900 dark:text-white font-semibold">{{ $selectedProfile?->anniversary ? $selectedProfile->anniversary->format('M d, Y') : 'Not Configured' }}</span>
                        </div>
                        <div class="md:col-span-2">
                            <span class="text-gray-500 block">Preferred Categories</span>
                            <span class="text-gray-900 dark:text-white font-semibold">
                                @if ($selectedProfile && is_array($selectedProfile->preferences))
                                    {{ $selectedProfile->preferences['preferred_brand'] ?? 'N/A' }} ({{ $selectedProfile->preferences['preferred_category'] ?? 'N/A' }})
                                @else
                                    General Spirits
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Wallet Adjustment Tool -->
                    <div class="p-4 border border-gray-200 dark:border-gray-800 rounded-lg bg-gray-50 dark:bg-gray-850/30 space-y-4">
                        <div class="flex justify-between items-center">
                            <h4 class="font-bold text-gray-950 dark:text-white text-sm">Customer Prepaid Wallet Adjustments</h4>
                            <span class="text-xs text-green-600 dark:text-green-400">Current Balance: <strong class="font-mono">₹{{ number_format($currentWalletBalance, 2) }}</strong></span>
                        </div>

                        @if (session()->has('wallet_success'))
                            <div class="text-xs text-green-600 font-semibold bg-green-50 p-2 border-l-4 border-green-500 rounded">
                                {{ session('wallet_success') }}
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Adjustment Mode</label>
                                <select wire:model="walletAdjustType" class="w-full rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5 px-3">
                                    <option value="deposit">Deposit (Refund / Add Credit)</option>
                                    <option value="withdrawal">Withdrawal (Charge / Deduct Credit)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Transaction Value (₹)</label>
                                <input type="number" step="0.01" min="0.01" wire:model.live="walletAmount" class="w-full rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5 px-3 text-center">
                            </div>
                            <div>
                                <button type="button" wire:click="adjustWalletBalance" class="w-full py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-xs cursor-pointer transition-colors">
                                    Submit Transaction
                                </button>
                            </div>
                        </div>
                        @error('walletAmount')
                            <p class="text-xs text-red-650 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-end">
                    <button type="button" wire:click="$set('showingProfileModal', false)" class="px-5 py-2 bg-indigo-650 hover:bg-indigo-750 text-white font-semibold rounded-lg cursor-pointer transition-colors">Close Profile</button>
                </div>
            </div>
        </div>
    @endif

    <!-- RESOLVE TICKET MODAL -->
    @if ($showingTicketModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-md rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Modify Ticket Registry</h3>
                    <button wire:click="$set('showingTicketModal', false)" class="text-gray-400 hover:text-gray-650 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-4">
                    <div>
                        <span class="text-xs text-gray-500 block font-semibold uppercase">Ticket Subject</span>
                        <span class="text-gray-900 dark:text-white font-bold">{{ $selectedTicket?->subject }}</span>
                    </div>

                    <div>
                        <span class="text-xs text-gray-500 block font-semibold uppercase">Customer Description</span>
                        <p class="text-sm text-gray-700 dark:text-gray-300 italic">"{{ $selectedTicket?->description }}"</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Update Ticket Status</label>
                        <select wire:model="newTicketStatus" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            <option value="open">Open</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved / Close Ticket</option>
                        </select>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showingTicketModal', false)" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg cursor-pointer">Cancel</button>
                    <button type="button" wire:click="updateTicketStatus" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg cursor-pointer transition-colors shadow">Save Changes</button>
                </div>
            </div>
        </div>
    @endif

    <!-- CREATE TICKET MODAL -->
    @if ($showingCreateTicketModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-2xl rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Create Support Ticket</h3>
                    <button wire:click="$set('showingCreateTicketModal', false)" class="text-gray-400 hover:text-gray-650 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-6">
                    <!-- Customer Search -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-850/50 border border-gray-200 dark:border-gray-800 rounded-lg">
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Search Customer Account</label>
                        <input type="text" wire:model.live.debounce.300ms="customerSearch" placeholder="Search customer by name or phone..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        
                        @if ($customerSuggestions->isNotEmpty())
                            <div class="mt-2 bg-white dark:bg-gray-850 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($customerSuggestions as $cs)
                                    <button type="button" wire:click="$set('ticketCustomerId', '{{ $cs->id }}'); $set('customerSearch', '{{ $cs->name }}')" class="w-full text-left px-4 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-950/20 text-sm flex justify-between items-center transition-colors">
                                        <div>
                                            <span class="text-gray-900 dark:text-white font-semibold">{{ $cs->name }}</span>
                                            <span class="text-gray-500 dark:text-gray-400 text-xs ml-2">({{ $cs->phone }})</span>
                                        </div>
                                        @if ($ticketCustomerId === $cs->id)
                                            <span class="text-xs bg-indigo-600 text-white font-bold px-2 py-0.5 rounded-full">Selected</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Ticket Type -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Ticket Type</label>
                            <select wire:model="ticketTypeField" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                                <option value="support">General Support / Query</option>
                                <option value="complaint">Complaint</option>
                                <option value="feedback">Feedback</option>
                            </select>
                        </div>

                        <!-- Ticket Priority -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Ticket Priority</label>
                            <select wire:model="ticketPriority" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                                <option value="low">Low Priority</option>
                                <option value="medium">Medium Priority</option>
                                <option value="high">High Priority</option>
                            </select>
                        </div>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Ticket Subject</label>
                        <input type="text" wire:model="ticketSubject" placeholder="Enter brief subject summary..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Ticket Description</label>
                        <textarea wire:model="ticketDescription" rows="4" placeholder="Enter details..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3"></textarea>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showingCreateTicketModal', false)" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg cursor-pointer">Cancel</button>
                    <button type="button" wire:click="saveTicket" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg cursor-pointer transition-colors shadow">Register Ticket</button>
                </div>
            </div>
        </div>
    @endif
</div>
