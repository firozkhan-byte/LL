<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ __('Supplier Management') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Onboard suppliers, manage credit boundaries, contacts, compliance metrics, and payments.') }}
            </p>
        </div>
        <div>
            <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer">
                {{ __('Onboard Supplier') }}
            </button>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
            <div class="p-3 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-lg">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <span class="block text-xs text-gray-400 font-semibold uppercase">{{ __('Total Suppliers') }}</span>
                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $metrics['total_suppliers'] }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-lg">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <span class="block text-xs text-gray-400 font-semibold uppercase">{{ __('Pending Approvals') }}</span>
                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $metrics['pending_approvals'] }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
            <div class="p-3 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-lg">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <span class="block text-xs text-gray-400 font-semibold uppercase">{{ __('Outstanding Balance') }}</span>
                <span class="text-2xl font-black text-gray-900 dark:text-white">₹{{ number_format($metrics['total_outstanding'], 2) }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-lg">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.906a1 1 0 00.95-.69l1.519-4.674z"/>
                </svg>
            </div>
            <div>
                <span class="block text-xs text-gray-400 font-semibold uppercase">{{ __('Average Rating') }}</span>
                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($metrics['average_rating'], 2) }}</span>
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

    <!-- Filtering panel -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <x-input-label for="search" :value="__('Search Suppliers')" />
                <x-text-input wire:model.live="search" id="search" type="text" class="mt-1 block w-full" placeholder="Search by name, code, GSTIN, PAN..." />
            </div>
            <div>
                <x-input-label for="filterStatus" :value="__('Status')" />
                <select wire:model.live="filterStatus" id="filterStatus" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2">
                    <option value="active">{{ __('Active Onboarded') }}</option>
                    <option value="pending_approval">{{ __('Pending Approval') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                    <option value="deleted">{{ __('Deleted') }}</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <x-input-label for="selectedRating" :value="__('Min Rating')" />
                    <select wire:model.live="selectedRating" id="selectedRating" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2">
                        <option value="">{{ __('Any') }}</option>
                        <option value="4.5">{{ __('4.5+ Stars') }}</option>
                        <option value="4.0">{{ __('4.0+ Stars') }}</option>
                        <option value="3.0">{{ __('3.0+ Stars') }}</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="selectedCreditDays" :value="__('Max Credit')" />
                    <select wire:model.live="selectedCreditDays" id="selectedCreditDays" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2">
                        <option value="">{{ __('Any') }}</option>
                        <option value="15">{{ __('15 Days') }}</option>
                        <option value="30">{{ __('30 Days') }}</option>
                        <option value="45">{{ __('45 Days') }}</option>
                        <option value="60">{{ __('60 Days') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Grid Listing -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Supplier Profile') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Primary Contact') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Compliance Identifiers') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Payment Terms') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Outstanding') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-extrabold text-gray-900 dark:text-white">{{ $supplier->name }}</div>
                                <div class="text-xs text-gray-400 font-mono">Code: {{ $supplier->code }}</div>
                                <div class="flex items-center space-x-1 mt-0.5">
                                    <svg class="h-3.5 w-3.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="text-xs font-semibold text-gray-500">{{ number_format($supplier->rating, 1) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                @if($supplier->primaryContact())
                                    <div class="font-semibold">{{ $supplier->primaryContact()->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $supplier->primaryContact()->phone }} &bull; {{ $supplier->primaryContact()->email }}</div>
                                @else
                                    <span class="text-gray-400 italic text-xs">{{ __('No Contacts') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="text-xs text-gray-700 dark:text-gray-300 font-semibold">GSTIN: <span class="font-mono text-indigo-600">{{ $supplier->gstin ?? __('N/A') }}</span></div>
                                <div class="text-[10px] text-gray-400">PAN: <span class="font-mono">{{ $supplier->pan ?? __('N/A') }}</span></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $supplier->payment_terms_days }} {{ __('Days Credit') }}</div>
                                <div class="text-xs text-gray-400">Limit: ₹{{ number_format($supplier->credit_limit, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-extrabold text-gray-900 dark:text-white">
                                ₹{{ number_format($supplier->outstanding_balance, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                    @if($supplier->status === 'active') bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-300
                                    @elseif($supplier->status === 'pending_approval') bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300
                                    @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 @endif">
                                    {{ str_replace('_', ' ', $supplier->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold">
                                @if($filterStatus === 'deleted')
                                    <button wire:click="restoreSupplier('{{ $supplier->id }}')" class="text-indigo-600 hover:text-indigo-900 cursor-pointer">
                                        {{ __('Restore') }}
                                    </button>
                                @else
                                    <button wire:click="openEditModal('{{ $supplier->id }}')" class="text-indigo-600 hover:text-indigo-900 mr-3 cursor-pointer">
                                        {{ __('Edit') }}
                                    </button>
                                    <button wire:click="deleteSupplier('{{ $supplier->id }}')" class="text-red-600 hover:text-red-900 cursor-pointer">
                                        {{ __('Delete') }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500 italic">
                                {{ __('No suppliers found onboarding in the catalogue.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t dark:border-gray-700">
            {{ $suppliers->links() }}
        </div>
    </div>

    <!-- Onboarding and Modification Modal -->
    @if($showingSupplierModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="$set('showingSupplierModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border dark:border-gray-700">
                    <form wire:submit="saveSupplier">
                        <div class="px-6 py-4 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $supplierId ? __('Modify Supplier Profile') : __('Onboard New Supplier') }}
                            </h3>
                        </div>

                        <div class="p-6 space-y-5 max-h-[550px] overflow-y-auto">
                            <!-- Basic Profile info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="form-name" :value="__('Supplier Legal Name')" />
                                    <x-text-input wire:model="name" id="form-name" class="mt-1 block w-full text-sm" required />
                                    <x-input-error :messages="$errors->get('name')" />
                                </div>
                                <div>
                                    <x-input-label for="form-status" :value="__('Onboarding status')" />
                                    <select wire:model="status" id="form-status" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2">
                                        <option value="pending_approval">{{ __('Pending Approval') }}</option>
                                        <option value="active">{{ __('Active') }}</option>
                                        <option value="inactive">{{ __('Inactive') }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Compliance tax info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-4 dark:border-gray-700">
                                <div>
                                    <x-input-label for="form-gstin" :value="__('GSTIN')" />
                                    <x-text-input wire:model="gstin" id="form-gstin" class="mt-1 block w-full text-sm" placeholder="e.g. 27AAACU1234A1Z1" />
                                </div>
                                <div>
                                    <x-input-label for="form-pan" :value="__('PAN Number')" />
                                    <x-text-input wire:model="pan" id="form-pan" class="mt-1 block w-full text-sm" placeholder="e.g. AAACU1234A" />
                                </div>
                            </div>

                            <!-- Payment parameters -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t pt-4 dark:border-gray-700">
                                <div>
                                    <x-input-label for="form-credit" :value="__('Payment terms (Credit Days)')" />
                                    <x-text-input wire:model="paymentTermsDays" id="form-credit" type="number" class="mt-1 block w-full text-sm" required />
                                </div>
                                <div>
                                    <x-input-label for="form-limit" :value="__('Credit Limit (₹)')" />
                                    <x-text-input wire:model="creditLimit" id="form-limit" type="number" step="0.01" class="mt-1 block w-full text-sm" required />
                                </div>
                                <div>
                                    <x-input-label for="form-rating" :value="__('Supplier Rating (0.0 to 5.0)')" />
                                    <x-text-input wire:model="rating" id="form-rating" type="number" step="0.1" class="mt-1 block w-full text-sm" required />
                                </div>
                            </div>

                            <!-- Contacts dynamic list -->
                            <div class="border-t pt-4 dark:border-gray-700 space-y-3">
                                <div class="flex justify-between items-center">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Supplier Contacts') }}</h4>
                                    <button type="button" wire:click="addContactLine" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">
                                        {{ __('+ Add Contact') }}
                                    </button>
                                </div>

                                <div class="space-y-2">
                                    @foreach($contactsList as $index => $contact)
                                        <div class="grid grid-cols-1 md:grid-cols-5 gap-2 items-center bg-gray-50 dark:bg-gray-900 p-3 rounded-lg border dark:border-gray-800">
                                            <input type="text" wire:model="contactsList.{{ $index }}.name" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" placeholder="Name" required />
                                            <input type="email" wire:model="contactsList.{{ $index }}.email" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" placeholder="Email" required />
                                            <input type="text" wire:model="contactsList.{{ $index }}.phone" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" placeholder="Phone" required />
                                            <input type="text" wire:model="contactsList.{{ $index }}.designation" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" placeholder="Designation" />
                                            <div class="flex items-center justify-between">
                                                <label class="flex items-center space-x-1">
                                                    <input type="checkbox" wire:model="contactsList.{{ $index }}.is_primary" class="rounded text-indigo-600 text-xs" />
                                                    <span class="text-[10px] text-gray-500">{{ __('Primary') }}</span>
                                                </label>
                                                <button type="button" wire:click="removeContactLine({{ $index }})" class="text-red-500 hover:text-red-700">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Banks dynamic list -->
                            <div class="border-t pt-4 dark:border-gray-700 space-y-3">
                                <div class="flex justify-between items-center">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Supplier Bank details') }}</h4>
                                    <button type="button" wire:click="addBankLine" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">
                                        {{ __('+ Add Bank Account') }}
                                    </button>
                                </div>

                                <div class="space-y-2">
                                    @foreach($bankAccountsList as $index => $bank)
                                        <div class="grid grid-cols-1 md:grid-cols-5 gap-2 items-center bg-gray-50 dark:bg-gray-900 p-3 rounded-lg border dark:border-gray-800">
                                            <input type="text" wire:model="bankAccountsList.{{ $index }}.bank_name" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" placeholder="Bank Name" required />
                                            <input type="text" wire:model="bankAccountsList.{{ $index }}.account_number" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" placeholder="Account Number" required />
                                            <input type="text" wire:model="bankAccountsList.{{ $index }}.ifsc_code" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" placeholder="IFSC Code" required />
                                            <input type="text" wire:model="bankAccountsList.{{ $index }}.branch_name" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs py-1.5" placeholder="Branch" />
                                            <div class="flex items-center justify-between">
                                                <label class="flex items-center space-x-1">
                                                    <input type="checkbox" wire:model="bankAccountsList.{{ $index }}.is_primary" class="rounded text-indigo-600 text-xs" />
                                                    <span class="text-[10px] text-gray-500">{{ __('Primary') }}</span>
                                                </label>
                                                <button type="button" wire:click="removeBankLine({{ $index }})" class="text-red-500 hover:text-red-700">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Compliance document attachments -->
                            <div class="border-t pt-4 dark:border-gray-700">
                                <x-input-label :value="__('Compliance Documents (GST certificate, PAN card etc.)')" />
                                <div class="mt-2 flex items-center space-x-2">
                                    <input wire:model="uploadedDocuments" type="file" multiple id="uploadedDocuments" class="hidden" />
                                    <label for="uploadedDocuments" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-xs font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer">
                                        {{ __('Choose files') }}
                                    </label>
                                    <span class="text-xs text-gray-400">
                                        {{ count($uploadedDocuments) }} {{ __('files selected') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t dark:border-gray-700">
                            <x-primary-button type="submit">{{ __('Save Profile') }}</x-primary-button>
                            <x-secondary-button wire:click="$set('showingSupplierModal', false)">{{ __('Cancel') }}</x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
