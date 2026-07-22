<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ __('GST & Excise Compliance Console') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Audit tax liabilities (GSTR1 & GSTR3B offsets), track state liquor excise licenses, manage transit permits, and maintain daily stock logs.') }}
            </p>
        </div>
        <div class="flex gap-2">
            <button wire:click="openPermitModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ __('Register Permit') }}
            </button>
            <button wire:click="openRegisterModal" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                </svg>
                {{ __('Update Daily Register') }}
            </button>
        </div>
    </div>

    <!-- Alerts -->
    @if (session()->has('permit_success'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-lg flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('permit_success') }}</span>
        </div>
    @endif
    @if (session()->has('license_success'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-lg flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('license_success') }}</span>
        </div>
    @endif
    @if (session()->has('reg_success'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-lg flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('reg_success') }}</span>
        </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200 dark:border-gray-700 flex space-x-8 overflow-x-auto">
        <button wire:click="setTab('dashboard')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'dashboard' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Compliance Dashboard') }}
        </button>
        <button wire:click="setTab('gst')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'gst' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('GST Tax Returns (GSTR1/3B)') }}
        </button>
        <button wire:click="setTab('register')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'register' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Daily Excise Register') }}
        </button>
        <button wire:click="setTab('permits')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'permits' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Licenses & Permits') }}
        </button>
    </div>

    <!-- Content -->
    <div class="space-y-6">
        @if ($activeTab === 'dashboard')
            <!-- Metrics grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- GST Net Due card -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Estimated Net GST Payable</span>
                        <h3 class="text-3xl font-black text-indigo-650 dark:text-indigo-400 font-mono mt-2">
                            ₹{{ number_format($gstSummary['net_gst_payable'], 2) }}
                        </h3>
                    </div>
                    <span class="text-xs text-gray-400 block mt-4">Calculated from Output GST offset against ITC.</span>
                </div>

                <!-- License Active count card -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Excise Licenses Status</span>
                        <div class="space-y-1 mt-2">
                            @foreach ($licensesList as $license)
                                <div class="flex justify-between text-sm">
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $license->license_number }}</span>
                                    <span class="text-xs {{ $license->expiry_date->diffInDays(now()) < 30 ? 'text-red-500 font-bold' : 'text-green-500' }}">
                                        Expires {{ $license->expiry_date->format('Y-m-d') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 block mt-4">Warnings shown if expiry is within 30 days.</span>
                </div>

                <!-- Tax summary quick widgets -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Input Credit & Output Tax</span>
                        <div class="mt-2 space-y-1.5 font-mono text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Collected (Output):</span>
                                <span class="text-indigo-650 dark:text-indigo-400 font-bold">₹{{ number_format($gstSummary['total_output_tax'], 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Paid (Input Credit):</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">₹{{ number_format($gstSummary['total_input_credit'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 block mt-4">Current month date range data.</span>
                </div>
            </div>
        @endif

        @if ($activeTab === 'gst')
            <!-- GST Date Selectors -->
            <div class="flex flex-col md:flex-row gap-4 bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Start Date</label>
                    <input type="date" wire:model.live="startDate" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-1.5 px-3">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">End Date</label>
                    <input type="date" wire:model.live="endDate" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-1.5 px-3">
                </div>
            </div>

            <!-- Tax Returns summaries -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- GSTR-1 Outward Supplies -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-950 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">GSTR-1 Outward Sales supplies</h3>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($gstSummary['gstr1_outward_supplies'] as $hsnCode => $details)
                            <div class="py-3 flex justify-between text-sm">
                                <div>
                                    <span class="font-bold text-gray-950 dark:text-white block">HSN/SAC: {{ $hsnCode }}</span>
                                    <span class="text-xs text-gray-500">Taxable Sales: ₹{{ number_format($details['taxable'], 2) }}</span>
                                </div>
                                <div class="text-right font-mono font-bold text-indigo-650 dark:text-indigo-400">
                                    <span>CGST/SGST: ₹{{ number_format($details['cgst'] + $details['sgst'], 2) }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-sm text-center py-6">No sales supplies during this range.</p>
                        @endforelse
                    </div>
                </div>

                <!-- GSTR-3B Credit Offset Sheet -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-950 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">GSTR-3B Offset Summary Sheet</h3>
                    <div class="space-y-3 font-mono text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Total Output Tax (Sales):</span>
                            <span class="font-bold text-gray-900 dark:text-white">₹{{ number_format($gstSummary['total_output_tax'], 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Total Input Credit (Purchases):</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">- ₹{{ number_format($gstSummary['total_input_credit'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-base font-black border-t border-gray-200 dark:border-gray-700 pt-3 text-indigo-650 dark:text-indigo-400">
                            <span>Net CGST/SGST Payable:</span>
                            <span>₹{{ number_format($gstSummary['net_gst_payable'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($activeTab === 'register')
            <!-- Register Filters -->
            <div class="flex flex-col md:flex-row gap-4 bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Filter Product SKU</label>
                    <select wire:model.live="filterProductId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-1.5 px-3">
                        <option value="">-- All Products --</option>
                        @foreach ($productsList as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Filter Date</label>
                    <input type="date" wire:model.live="filterDate" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-1.5 px-3">
                </div>
            </div>

            <!-- Daily Register Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Product Name</th>
                                <th class="px-6 py-3 text-right">Opening Qty</th>
                                <th class="px-6 py-3 text-right">Received Qty</th>
                                <th class="px-6 py-3 text-right">Sold Qty</th>
                                <th class="px-6 py-3 text-right">Closing Qty</th>
                                <th class="px-6 py-3 text-right">Excise Duty Paid</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($registerLines as $line)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors text-sm">
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400 font-mono">{{ $line->transaction_date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-950 dark:text-white">{{ $line->product->name }}</td>
                                    <td class="px-6 py-4 text-right font-mono text-gray-700 dark:text-gray-300">{{ number_format($line->opening_balance, 0) }}</td>
                                    <td class="px-6 py-4 text-right font-mono text-indigo-650 dark:text-indigo-400 font-semibold">+{{ number_format($line->received_quantity, 0) }}</td>
                                    <td class="px-6 py-4 text-right font-mono text-red-500 font-semibold">-{{ number_format($line->sold_quantity, 0) }}</td>
                                    <td class="px-6 py-4 text-right font-mono text-gray-950 dark:text-white font-bold">{{ number_format($line->closing_balance, 0) }}</td>
                                    <td class="px-6 py-4 text-right font-mono text-emerald-600 dark:text-emerald-400 font-bold">₹{{ number_format($line->excise_duty_paid, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No excise register rows logged.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div>
                {{ $registerLines->links() }}
            </div>
        @endif

        @if ($activeTab === 'permits')
            <!-- Permits List Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Permit #</th>
                                <th class="px-6 py-3">GL License</th>
                                <th class="px-6 py-3">Supplier Name</th>
                                <th class="px-6 py-3">Issue Date</th>
                                <th class="px-6 py-3">Expiry Date</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($permitsList as $permit)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors text-sm">
                                    <td class="px-6 py-4 font-mono font-bold text-indigo-650 dark:text-indigo-400">{{ $permit->permit_number }}</td>
                                    <td class="px-6 py-4 text-gray-800 dark:text-gray-300 font-semibold">{{ $permit->license->license_number }}</td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-white font-bold">{{ $permit->supplier->name }}</td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400 font-mono">{{ $permit->issue_date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400 font-mono">{{ $permit->expiry_date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2.5 py-0.5 text-xs font-bold uppercase rounded-full
                                            {{ $permit->status === 'utilized' ? 'bg-green-105 text-green-700 dark:text-green-400' : 'bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400' }}
                                        ">
                                            {{ $permit->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($permit->status === 'pending')
                                            <button wire:click="utilizePermit('{{ $permit->id }}')" class="text-xs bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 px-2 py-1 font-bold rounded cursor-pointer transition">
                                                Utilize Permit
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400 italic">No action</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No transit permits logged.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div>
                {{ $permitsList->links() }}
            </div>
        @endif
    </div>

    <!-- REGISTER TRANSIT PERMIT MODAL -->
    @if ($showingPermitModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-md rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Register Excise Transport Permit</h3>
                    <button wire:click="$set('showingPermitModal', false)" class="text-gray-400 hover:text-gray-650 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Permit Number</label>
                        <input type="text" wire:model="permitNumber" placeholder="e.g. PRM-MH-1234" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        @error('permitNumber') <p class="text-xs text-red-650 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Excise License</label>
                        <select wire:model="permitLicenseId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            @foreach ($licensesList as $license)
                                <option value="{{ $license->id }}">{{ $license->license_number }} ({{ $license->license_type }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Supplier</label>
                        <select wire:model="permitSupplierId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            @foreach ($suppliersList as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Issue Date</label>
                            <input type="date" wire:model="permitIssueDate" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Expiry Date</label>
                            <input type="date" wire:model="permitExpiryDate" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showingPermitModal', false)" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg cursor-pointer">Cancel</button>
                    <button type="button" wire:click="savePermit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg cursor-pointer transition-colors shadow">Save Permit</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MANUALLY TRIGGER REGISTER CALCULATION -->
    @if ($showingRegisterModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-md rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Calculate Daily Excise Register</h3>
                    <button wire:click="$set('showingRegisterModal', false)" class="text-gray-400 hover:text-gray-650 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Target Product SKU</label>
                        <select wire:model="regProductId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            @foreach ($productsList as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Excise License</label>
                        <select wire:model="regLicenseId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            @foreach ($licensesList as $license)
                                <option value="{{ $license->id }}">{{ $license->license_number }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Transaction Date</label>
                        <input type="date" wire:model="regDate" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showingRegisterModal', false)" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg cursor-pointer">Cancel</button>
                    <button type="button" wire:click="triggerRegisterCalculation" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg cursor-pointer transition-colors shadow">Calculate & Post</button>
                </div>
            </div>
        </div>
    @endif
</div>
