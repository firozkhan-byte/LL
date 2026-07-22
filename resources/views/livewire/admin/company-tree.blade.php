<div class="p-6 max-w-7xl mx-auto space-y-6" x-data="{ expanded: {} }">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ __('Enterprise Structure Tree') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Explore the organization hierarchy, regional offices, branches, stores, and warehouses.') }}
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <button @click="expanded = {}" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-xs font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer">
                {{ __('Collapse All') }}
            </button>
        </div>
    </div>

    <!-- Tree View Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="space-y-6">
            @forelse($companies as $company)
                <div x-data="{ openCompany: true }">
                    <!-- Company Row -->
                    <div class="flex items-center space-x-3 py-2 px-3 bg-gray-50 dark:bg-gray-900/60 rounded-lg border border-gray-100 dark:border-gray-800">
                        <button @click="openCompany = !openCompany" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                            <svg class="h-5 w-5 transform transition" :class="{'rotate-90': openCompany}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <div class="flex-1">
                            <span class="text-base font-extrabold text-gray-900 dark:text-white">{{ $company->name }}</span>
                            <span class="ml-2 text-xs font-mono text-gray-400 bg-gray-200/50 dark:bg-gray-800 px-2 py-0.5 rounded">{{ $company->registration_number ?? __('N/A') }}</span>
                        </div>
                    </div>

                    <!-- Regional Offices & Children -->
                    <div x-show="openCompany" class="pl-8 mt-3 space-y-4 border-l border-gray-200 dark:border-gray-700 ml-6">
                        @forelse($company->regionalOffices as $ro)
                            <div x-data="{ openRo: true }">
                                <!-- RO Row -->
                                <div class="flex items-center space-x-2.5 py-1 px-2.5 hover:bg-gray-50/50 dark:hover:bg-gray-700/30 rounded transition">
                                    <button @click="openRo = !openRo" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                                        <svg class="h-4 w-4 transform transition" :class="{'rotate-90': openRo}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                    <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <div>
                                        <span class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $ro->name }}</span>
                                        <span class="ml-1 text-xs text-gray-400">({{ $ro->code }})</span>
                                    </div>
                                </div>

                                <!-- Branches -->
                                <div x-show="openRo" class="pl-6 mt-2 space-y-3 border-l border-gray-200 dark:border-gray-700/60 ml-4">
                                    @forelse($ro->branches as $branch)
                                        <div x-data="{ openBranch: true }">
                                            <!-- Branch Row -->
                                            <div class="flex items-center space-x-2 py-1 px-2 hover:bg-gray-50/50 dark:hover:bg-gray-700/30 rounded transition">
                                                <button @click="openBranch = !openBranch" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                                                    <svg class="h-3.5 w-3.5 transform transition" :class="{'rotate-90': openBranch}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </button>
                                                <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-300">{{ $branch->name }}</span>
                                                    <span class="text-xs text-gray-400">({{ $branch->code }})</span>
                                                    <span class="px-1.5 py-0.25 rounded text-[10px] font-semibold uppercase tracking-wider
                                                        @if($branch->status === 'active') bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-300
                                                        @elseif($branch->status === 'inactive') bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                                        @else bg-yellow-50 dark:bg-yellow-950/20 text-yellow-700 dark:text-yellow-300 @endif">
                                                        {{ $branch->status }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Stores & Warehouses -->
                                            <div x-show="openBranch" class="pl-6 mt-1.5 grid grid-cols-1 md:grid-cols-2 gap-3 border-l border-gray-200 dark:border-gray-800 ml-3.5">
                                                <!-- Stores List -->
                                                <div class="space-y-1.5">
                                                    <div class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                                        {{ __('Stores') }}
                                                    </div>
                                                    @forelse($branch->stores as $store)
                                                        <div class="flex items-center space-x-2 py-1 pl-2 bg-indigo-50/20 dark:bg-indigo-950/10 rounded border border-indigo-100/30 dark:border-indigo-900/10">
                                                            <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                            </svg>
                                                            <div class="flex-1 min-w-0">
                                                                <div class="text-xs font-medium text-gray-900 dark:text-gray-300 truncate">{{ $store->name }}</div>
                                                                @if($store->license_number)
                                                                    <div class="text-[10px] text-gray-400 truncate">{{ __('Excise:') }} {{ $store->license_number }}</div>
                                                                @endif
                                                            </div>
                                                            <span class="mr-2 px-1 rounded text-[9px] font-bold uppercase
                                                                @if($store->status === 'active') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                                                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 @endif">
                                                                {{ $store->status === 'active' ? __('Act') : __('Pend') }}
                                                            </span>
                                                        </div>
                                                    @empty
                                                        <div class="text-xs text-gray-400 italic pl-2">{{ __('No mapped stores.') }}</div>
                                                    @endforelse
                                                </div>

                                                <!-- Warehouses List -->
                                                <div class="space-y-1.5">
                                                    <div class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                                        {{ __('Warehouses') }}
                                                    </div>
                                                    @forelse($branch->warehouses as $warehouse)
                                                        <div class="flex items-center space-x-2 py-1 pl-2 bg-emerald-50/20 dark:bg-emerald-950/10 rounded border border-emerald-100/30 dark:border-emerald-900/10">
                                                            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                            </svg>
                                                            <div class="flex-1 min-w-0">
                                                                <div class="text-xs font-medium text-gray-900 dark:text-gray-300 truncate">{{ $warehouse->name }}</div>
                                                                <div class="text-[10px] text-gray-400 truncate">Code: {{ $warehouse->code }}</div>
                                                            </div>
                                                            <span class="mr-2 px-1 rounded text-[9px] font-bold uppercase
                                                                @if($warehouse->status === 'active') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                                                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 @endif">
                                                                {{ $warehouse->status === 'active' ? __('Act') : __('Pend') }}
                                                            </span>
                                                        </div>
                                                    @empty
                                                        <div class="text-xs text-gray-400 italic pl-2">{{ __('No mapped warehouses.') }}</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-xs text-gray-400 italic pl-4">{{ __('No branches registered under this office.') }}</div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-gray-400 italic pl-4">{{ __('No regional offices registered.') }}</div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-sm text-gray-500 italic">
                    {{ __('No companies found in system. Seed your database to populate records.') }}
                </div>
            @endforelse
        </div>
    </div>
</div>
