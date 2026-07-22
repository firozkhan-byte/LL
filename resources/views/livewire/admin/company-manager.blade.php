<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            {{ __('Company Management & Mapping') }}
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ __('Setup corporate parameters, map stores, map warehouses, and define organization structures.') }}
        </p>
    </div>

    <!-- Alert Message -->
    @if (session()->has('message'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-lg flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8">
            <button wire:click="$set('activeTab', 'company')" class="pb-4 px-1 border-b-2 font-medium text-sm transition cursor-pointer
                {{ $activeTab === 'company' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400' }}">
                {{ __('Company Profile & Settings') }}
            </button>
            <button wire:click="$set('activeTab', 'branches')" class="pb-4 px-1 border-b-2 font-medium text-sm transition cursor-pointer
                {{ $activeTab === 'branches' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400' }}">
                {{ __('Branches & Mapped Outlets') }}
            </button>
            <button wire:click="$set('activeTab', 'structures')" class="pb-4 px-1 border-b-2 font-medium text-sm transition cursor-pointer
                {{ $activeTab === 'structures' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400' }}">
                {{ __('Corporate Metadata') }}
            </button>
        </nav>
    </div>

    <!-- TAB 1: Company Profile & Settings -->
    @if($activeTab === 'company')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <form wire:submit="updateCompany" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- General Details -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b pb-2">{{ __('General Info') }}</h3>
                        
                        <div>
                            <x-input-label for="companyName" :value="__('Company Registered Name')" />
                            <x-text-input wire:model="companyName" id="companyName" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('companyName')" />
                        </div>

                        <div>
                            <x-input-label for="registrationNumber" :value="__('Company Reg No (CIN)')" />
                            <x-text-input wire:model="registrationNumber" id="registrationNumber" class="mt-1 block w-full" />
                        </div>

                        <div>
                            <x-input-label for="taxNumber" :value="__('GSTIN / Tax Registration No')" />
                            <x-text-input wire:model="taxNumber" id="taxNumber" class="mt-1 block w-full" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="email" :value="__('Corporate Email')" />
                                <x-text-input wire:model="email" id="email" type="email" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="phone" :value="__('Corporate Phone')" />
                                <x-text-input wire:model="phone" id="phone" class="mt-1 block w-full" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="website" :value="__('Corporate Website')" />
                            <x-text-input wire:model="website" id="website" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <!-- Localization & Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b pb-2">{{ __('System & Location Settings') }}</h3>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="currency" :value="__('Base Currency')" />
                                <x-text-input wire:model="currency" id="currency" class="mt-1 block w-full" placeholder="INR" required />
                            </div>
                            <div>
                                <x-input-label for="timezone" :value="__('Local Timezone')" />
                                <x-text-input wire:model="timezone" id="timezone" class="mt-1 block w-full" placeholder="Asia/Kolkata" required />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="addressLine1" :value="__('Address Line 1')" />
                            <x-text-input wire:model="addressLine1" id="addressLine1" class="mt-1 block w-full" />
                        </div>

                        <div>
                            <x-input-label for="addressLine2" :value="__('Address Line 2')" />
                            <x-text-input wire:model="addressLine2" id="addressLine2" class="mt-1 block w-full" />
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-1">
                                <x-input-label for="city" :value="__('City')" />
                                <x-text-input wire:model="city" id="city" class="mt-1 block w-full" />
                            </div>
                            <div class="col-span-1">
                                <x-input-label for="state" :value="__('State')" />
                                <x-text-input wire:model="state" id="state" class="mt-1 block w-full" />
                            </div>
                            <div class="col-span-1">
                                <x-input-label for="postalCode" :value="__('Pincode')" />
                                <x-text-input wire:model="postalCode" id="postalCode" class="mt-1 block w-full" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t">
                    <x-primary-button type="submit">{{ __('Save Settings') }}</x-primary-button>
                </div>
            </form>
        </div>
    @endif

    <!-- TAB 2: Branches & Mapped Outlets -->
    @if($activeTab === 'branches')
        <!-- Action Buttons Bar -->
        <div class="flex flex-wrap gap-3 items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Corporate Structure Mappings') }}</h3>
            <div class="flex items-center space-x-2">
                <button wire:click="$set('showingBranchModal', true)" class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow cursor-pointer">
                    {{ __('Propose New Branch') }}
                </button>
                <button wire:click="$set('showingStoreModal', true)" class="inline-flex items-center px-3 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-lg text-xs font-semibold shadow cursor-pointer">
                    {{ __('Propose New Store') }}
                </button>
                <button wire:click="$set('showingWarehouseModal', true)" class="inline-flex items-center px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow cursor-pointer">
                    {{ __('Propose New Warehouse') }}
                </button>
            </div>
        </div>

        <div class="p-4 bg-indigo-50 dark:bg-indigo-950/30 text-indigo-800 dark:text-indigo-300 rounded-lg text-xs flex items-center space-x-2">
            <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ __('Note: Creating branches, stores, or warehouses will submit a proposal request to the CEO / Approvals Inbox. They will appear on dashboard listings once approved.') }}</span>
        </div>

        <!-- Branches Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Branch') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Office Region') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Mapped Stores') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Mapped Warehouses') }}</th>
                            <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($branches as $branch)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $branch->name }}</div>
                                    <div class="text-xs text-gray-400">Code: {{ $branch->code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-350">
                                    {{ $branch->regionalOffice->name ?? __('N/A') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @forelse($branch->stores as $store)
                                            <span class="px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 text-[10px] font-medium">
                                                {{ $store->name }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400 italic">{{ __('None') }}</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @forelse($branch->warehouses as $wh)
                                            <span class="px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[10px] font-medium">
                                                {{ $wh->name }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400 italic">{{ __('None') }}</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        @if($branch->status === 'active') bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-300
                                        @elseif($branch->status === 'inactive') bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                        @else bg-yellow-50 dark:bg-yellow-950/20 text-yellow-700 dark:text-yellow-300 @endif">
                                        {{ $branch->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 italic">
                                    {{ __('No branches registered.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- TAB 3: Corporate Metadata -->
    @if($activeTab === 'structures')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Add Structure Item Card -->
            <div class="md:col-span-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-4 h-fit">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b pb-2">{{ __('Add Struct Element') }}</h3>
                <form wire:submit="addStructure" class="space-y-4">
                    <div>
                        <x-input-label for="structureType" :value="__('Select Element Type')" />
                        <select wire:model.live="structureType" id="structureType" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2">
                            <option value="department">{{ __('Department') }}</option>
                            <option value="business_unit">{{ __('Business Unit') }}</option>
                            <option value="cost_center">{{ __('Cost Center') }}</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="structName" :value="__('Name')" />
                        <x-text-input wire:model="structName" id="structName" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('structName')" />
                    </div>

                    <div>
                        <x-input-label for="structCode" :value="__('Unique Code')" />
                        <x-text-input wire:model="structCode" id="structCode" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('structCode')" />
                    </div>

                    @if($structureType === 'cost_center')
                        <div>
                            <x-input-label for="structParentId" :value="__('Map Business Unit')" />
                            <select wire:model="structParentId" id="structParentId" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2">
                                <option value="">-- {{ __('Select Business Unit') }} --</option>
                                @foreach($businessUnits as $bu)
                                    <option value="{{ $bu->id }}">{{ $bu->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('structParentId')" />
                        </div>
                    @endif

                    <div class="pt-2">
                        <x-primary-button class="w-full justify-center">{{ __('Create Element') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Structure Listings -->
            <div class="md:col-span-2 space-y-6">
                <!-- Departments List -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <h4 class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">{{ __('Departments') }}</h4>
                    <div class="grid grid-cols-2 gap-3">
                        @forelse($departments as $dept)
                            <div class="p-3 bg-gray-50 dark:bg-gray-900/40 rounded border dark:border-gray-800 flex justify-between">
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $dept->name }}</span>
                                <span class="text-xs font-mono text-gray-400">{{ $dept->code }}</span>
                            </div>
                        @empty
                            <p class="col-span-2 text-sm text-gray-400 italic">{{ __('No departments registered.') }}</p>
                        @endforelse
                    </div>
                </div>

                <!-- Business Units & Cost Centers -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <h4 class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">{{ __('Business Units & Mapped Cost Centers') }}</h4>
                    <div class="space-y-4">
                        @forelse($businessUnits as $bu)
                            <div class="border rounded-lg p-4 dark:border-gray-700">
                                <div class="flex justify-between items-center border-b pb-2 mb-3">
                                    <span class="text-sm font-extrabold text-gray-900 dark:text-white">{{ $bu->name }}</span>
                                    <span class="text-xs font-mono text-gray-400">BU Code: {{ $bu->code }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 pl-4">
                                    @php $buCostCenters = $costCenters->where('business_unit_id', $bu->id); @endphp
                                    @forelse($buCostCenters as $cc)
                                        <div class="p-2 bg-indigo-50/20 dark:bg-indigo-950/10 border border-indigo-100/30 rounded text-xs flex justify-between">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $cc->name }}</span>
                                            <span class="font-mono text-gray-400">{{ $cc->code }}</span>
                                        </div>
                                    @empty
                                        <p class="text-xs text-gray-400 italic">{{ __('No cost centers mapped.') }}</p>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 italic">{{ __('No business units registered.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 1: Propose Branch -->
    @if($showingBranchModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="$set('showingBranchModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border dark:border-gray-700">
                    <form wire:submit="proposeBranch">
                        <div class="px-6 py-4 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Propose New Branch') }}</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <x-input-label for="branchRoId" :value="__('Regional Office Location')" />
                                <select wire:model="branchRoId" id="branchRoId" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2">
                                    <option value="">-- {{ __('Select Region') }} --</option>
                                    @foreach($roList as $ro)
                                        <option value="{{ $ro->id }}">{{ $ro->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('branchRoId')" />
                            </div>
                            <div>
                                <x-input-label for="branchName" :value="__('Branch Name')" />
                                <x-text-input wire:model="branchName" id="branchName" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('branchName')" />
                            </div>
                            <div>
                                <x-input-label for="branchCode" :value="__('Branch Code')" />
                                <x-text-input wire:model="branchCode" id="branchCode" class="mt-1 block w-full" placeholder="BR-XXX-YY" required />
                                <x-input-error :messages="$errors->get('branchCode')" />
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t dark:border-gray-700">
                            <x-primary-button type="submit">{{ __('Propose') }}</x-primary-button>
                            <x-secondary-button wire:click="$set('showingBranchModal', false)">{{ __('Cancel') }}</x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 2: Propose Store -->
    @if($showingStoreModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="$set('showingStoreModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border dark:border-gray-700">
                    <form wire:submit="proposeStore">
                        <div class="px-6 py-4 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Propose New Mapped Store') }}</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <x-input-label for="storeBranchId" :value="__('Assign to Branch')" />
                                <select wire:model="storeBranchId" id="storeBranchId" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2">
                                    <option value="">-- {{ __('Select Branch') }} --</option>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('storeBranchId')" />
                            </div>
                            <div>
                                <x-input-label for="storeName" :value="__('Store Name')" />
                                <x-text-input wire:model="storeName" id="storeName" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('storeName')" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="storeCode" :value="__('Store Code')" />
                                    <x-text-input wire:model="storeCode" id="storeCode" class="mt-1 block w-full" placeholder="ST-XXX" required />
                                    <x-input-error :messages="$errors->get('storeCode')" />
                                </div>
                                <div>
                                    <x-input-label for="storeLicense" :value="__('Excise License Code')" />
                                    <x-text-input wire:model="storeLicense" id="storeLicense" class="mt-1 block w-full" placeholder="EX-XXX-2026" />
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t dark:border-gray-700">
                            <x-primary-button type="submit">{{ __('Propose') }}</x-primary-button>
                            <x-secondary-button wire:click="$set('showingStoreModal', false)">{{ __('Cancel') }}</x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 3: Propose Warehouse -->
    @if($showingWarehouseModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="$set('showingWarehouseModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border dark:border-gray-700">
                    <form wire:submit="proposeWarehouse">
                        <div class="px-6 py-4 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Propose New Mapped Warehouse') }}</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <x-input-label for="warehouseBranchId" :value="__('Assign to Branch')" />
                                <select wire:model="warehouseBranchId" id="warehouseBranchId" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2">
                                    <option value="">-- {{ __('Select Branch') }} --</option>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('warehouseBranchId')" />
                            </div>
                            <div>
                                <x-input-label for="warehouseName" :value="__('Warehouse Name')" />
                                <x-text-input wire:model="warehouseName" id="warehouseName" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('warehouseName')" />
                            </div>
                            <div>
                                <x-input-label for="warehouseCode" :value="__('Warehouse Code')" />
                                <x-text-input wire:model="warehouseCode" id="warehouseCode" class="mt-1 block w-full" placeholder="WH-XXX" required />
                                <x-input-error :messages="$errors->get('warehouseCode')" />
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t dark:border-gray-700">
                            <x-primary-button type="submit">{{ __('Propose') }}</x-primary-button>
                            <x-secondary-button wire:click="$set('showingWarehouseModal', false)">{{ __('Cancel') }}</x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
