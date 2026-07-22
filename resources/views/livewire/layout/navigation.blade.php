<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div x-data="{ mobileOpen: false }">
    <!-- Desktop Sidebar -->
    <aside class="hidden md:flex md:flex-col md:w-64 md:fixed md:inset-y-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">
        <!-- Logo Header -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5" wire:navigate>
                <x-application-logo class="h-8 w-auto fill-current text-indigo-600 dark:text-indigo-400" />
                <span class="font-extrabold text-base text-gray-900 dark:text-white tracking-wide">LivingLiquidz</span>
            </a>
        </div>

        <!-- Navigation Links Stack -->
        <div class="flex-1 flex flex-col justify-between py-6 overflow-y-auto px-4">
            <div class="space-y-6">
                <!-- Group 1: General -->
                <div class="space-y-1.5">
                    <span class="px-3 text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ __('Main') }}</span>
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                </div>

                <!-- Group 2: Administration -->
                @if(auth()->user()->can('manage-users') || auth()->user()->can('manage-company') || auth()->user()->can('manage-products') || auth()->user()->hasAnyRole(['Super Admin', 'CEO', 'Finance Manager']))
                <div class="space-y-1.5">
                    <span class="px-3 text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ __('ERP Operations') }}</span>
                    
                    @can('manage-company')
                        <a href="{{ route('admin.company-tree') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.company-tree') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span>{{ __('Org Tree') }}</span>
                        </a>
                        <a href="{{ route('admin.company-manager') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.company-manager') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span>{{ __('Company Settings') }}</span>
                        </a>
                    @endcan

                    @can('manage-products')
                        <a href="{{ route('admin.products') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.products') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span>{{ __('Products') }}</span>
                        </a>
                    @endcan

                    @can('manage-suppliers')
                        <a href="{{ route('admin.suppliers') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.suppliers') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>{{ __('Suppliers') }}</span>
                        </a>
                    @endcan

                    <a href="{{ route('admin.purchase') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.purchase') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span>{{ __('Purchase') }}</span>
                    </a>

                    <a href="{{ route('admin.warehouse') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.warehouse') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span>{{ __('Warehouse') }}</span>
                    </a>

                    <a href="{{ route('admin.inventory') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.inventory') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ __('Inventory') }}</span>
                    </a>

                    <a href="{{ route('admin.pos') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.pos') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ __('POS Terminal') }}</span>
                    </a>

                    <a href="{{ route('admin.sales') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.sales') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>{{ __('Sales Hub') }}</span>
                    </a>

                    <a href="{{ route('admin.crm') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.crm') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>{{ __('Customer CRM') }}</span>
                    </a>

                    <a href="{{ route('admin.finance') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.finance') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ __('Finance Hub') }}</span>
                    </a>

                    <a href="{{ route('admin.compliance') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.compliance') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>{{ __('GST & Excise') }}</span>
                    </a>

                    <a href="{{ route('admin.hrm') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.hrm') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>{{ __('HRM Console') }}</span>
                    </a>

                    <a href="{{ route('admin.delivery') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.delivery') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>{{ __('Delivery Hub') }}</span>
                    </a>

                    <a href="{{ route('admin.reports') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.reports') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>{{ __('Analytics & Reports') }}</span>
                    </a>

                    <a href="{{ route('admin.ai') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.ai') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        <span>{{ __('AI Console') }}</span>
                    </a>

                    @if(auth()->user()->hasAnyRole(['Super Admin', 'CEO', 'Finance Manager']))
                        <a href="{{ route('admin.approvals') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.approvals') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>{{ __('Approvals') }}</span>
                        </a>
                    @endif

                    @can('manage-users')
                        <a href="{{ route('admin.users') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.users') ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white' }}" wire:navigate>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>{{ __('Users') }}</span>
                        </a>
                    @endcan
                </div>
                @endif
            </div>

            <!-- Footer: Profile Info / Logout -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 flex flex-col space-y-4">
                <a href="{{ route('profile') }}" class="flex items-center space-x-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 p-2 rounded-lg transition" wire:navigate>
                    <div class="h-9 w-9 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-gray-400 truncate">{{ auth()->user()->email }}</div>
                    </div>
                </a>
                <button wire:click="logout" class="flex w-full items-center space-x-3 px-3 py-2 rounded-lg text-sm font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20 transition cursor-pointer">
                    <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>{{ __('Log Out') }}</span>
                </button>
            </div>
        </div>
    </aside>

    <!-- Mobile Top Navigation Header -->
    <header class="md:hidden bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 h-16 px-4 flex items-center justify-between sticky top-0 z-40">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2" wire:navigate>
            <x-application-logo class="h-7 w-auto fill-current text-indigo-600 dark:text-indigo-400" />
            <span class="font-extrabold text-sm text-gray-900 dark:text-white tracking-wide">LivingLiquidz</span>
        </a>
        <button @click="mobileOpen = !mobileOpen" class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{'hidden': mobileOpen, 'inline-flex': !mobileOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{'hidden': !mobileOpen, 'inline-flex': mobileOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </header>

    <!-- Mobile Menu Slide-Over Drawer -->
    <div x-show="mobileOpen" class="md:hidden fixed inset-0 z-50 flex" style="display: none;">
        <!-- Backdrop overlay -->
        <div x-show="mobileOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-600/75 dark:bg-gray-900/80" @click="mobileOpen = false"></div>

        <!-- Drawer Content Panel -->
        <div x-show="mobileOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex-1 flex flex-col max-w-xs w-full bg-white dark:bg-gray-800 border-r dark:border-gray-700 pt-5 pb-4">
            <!-- Close Button Inside Panel -->
            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button @click="mobileOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Drawer Logo Header -->
            <div class="flex-shrink-0 flex items-center px-4">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2" wire:navigate>
                    <x-application-logo class="h-8 w-auto fill-current text-indigo-600 dark:text-indigo-400" />
                    <span class="font-extrabold text-base text-gray-900 dark:text-white">LivingLiquidz</span>
                </a>
            </div>

            <!-- Drawer Links Stack -->
            <div class="mt-8 flex-1 h-0 overflow-y-auto px-2 space-y-1">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                
                @can('manage-company')
                    <x-responsive-nav-link :href="route('admin.company-tree')" :active="request()->routeIs('admin.company-tree')" wire:navigate>
                        {{ __('Org Tree') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.company-manager')" :active="request()->routeIs('admin.company-manager')" wire:navigate>
                        {{ __('Company Settings') }}
                    </x-responsive-nav-link>
                @endcan

                @can('manage-products')
                    <x-responsive-nav-link :href="route('admin.products')" :active="request()->routeIs('admin.products')" wire:navigate>
                        {{ __('Products') }}
                    </x-responsive-nav-link>
                @endcan

                @can('manage-suppliers')
                    <x-responsive-nav-link :href="route('admin.suppliers')" :active="request()->routeIs('admin.suppliers')" wire:navigate>
                        {{ __('Suppliers') }}
                    </x-responsive-nav-link>
                @endcan

                <x-responsive-nav-link :href="route('admin.purchase')" :active="request()->routeIs('admin.purchase')" wire:navigate>
                    {{ __('Purchase') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.warehouse')" :active="request()->routeIs('admin.warehouse')" wire:navigate>
                    {{ __('Warehouse') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.inventory')" :active="request()->routeIs('admin.inventory')" wire:navigate>
                    {{ __('Inventory') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.pos')" :active="request()->routeIs('admin.pos')" wire:navigate>
                    {{ __('POS Terminal') }}
                </x-responsive-nav-link>

                @if(auth()->user()->hasAnyRole(['Super Admin', 'CEO', 'Finance Manager']))
                    <x-responsive-nav-link :href="route('admin.approvals')" :active="request()->routeIs('admin.approvals')" wire:navigate>
                        {{ __('Approvals') }}
                    </x-responsive-nav-link>
                @endif

                @can('manage-users')
                    <x-responsive-nav-link :href="route('admin.users')" :active="request()->routeIs('admin.users')" wire:navigate>
                        {{ __('Users') }}
                    </x-responsive-nav-link>
                @endcan
            </div>

            <!-- Drawer Profile Footer / Logout -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 px-4 flex flex-col space-y-4">
                <a href="{{ route('profile') }}" class="flex items-center space-x-3" wire:navigate>
                    <div class="h-9 w-9 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="text-xs font-bold text-gray-900 dark:text-white">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-gray-500">{{ auth()->user()->email }}</div>
                    </div>
                </a>
                <button wire:click="logout" class="flex w-full items-center space-x-3 px-3 py-2 rounded-lg text-sm font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20 transition cursor-pointer">
                    <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>{{ __('Log Out') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>
