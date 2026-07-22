<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ __('User Management') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Manage ERP users, roles, permissions, status, and view audit trails.') }}
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition duration-200 cursor-pointer">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('Add New User') }}
            </button>
        </div>
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

    <!-- Search & Filters Panel -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="relative col-span-1 md:col-span-2">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name or email..." class="pl-10 w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2.5" />
            </div>

            <!-- Role Filter -->
            <div>
                <select wire:model.live="filterRole" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2.5">
                    <option value="">{{ __('All Roles') }}</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select wire:model.live="filterStatus" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2.5">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="inactive">{{ __('Inactive') }}</option>
                    <option value="suspended">{{ __('Suspended') }}</option>
                </select>
            </div>
        </div>

        <div class="mt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0 pt-4 border-t border-gray-100 dark:border-gray-700">
            <!-- Trash Toggle -->
            <div>
                <select wire:model.live="filterTrashed" class="rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2">
                    <option value="">{{ __('Active Users') }}</option>
                    <option value="with">{{ __('All (Inc. Deleted)') }}</option>
                    <option value="only">{{ __('Deleted Only') }}</option>
                </select>
            </div>

            <!-- Excel Import/Export Buttons -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Excel Export -->
                <button wire:click="exportUsers" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer">
                    <svg class="h-5 w-5 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    {{ __('Export Excel') }}
                </button>

                <!-- Excel Import Form -->
                <div class="flex items-center space-x-2">
                    <input wire:model="importFile" type="file" id="importFile" class="hidden" />
                    <label for="importFile" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer">
                        <svg class="h-5 w-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        {{ $importFile ? $importFile->getClientOriginalName() : __('Select File') }}
                    </label>
                    
                    @if($importFile)
                        <button wire:click="importUsers" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition cursor-pointer">
                            {{ __('Import') }}
                        </button>
                    @endif
                </div>
                <x-input-error class="text-xs" :messages="$errors->get('importFile')" />
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('User Info') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Roles') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Last Login') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        @if($user->avatar_path)
                                            <img class="h-10 w-10 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700" src="{{ Storage::url($user->avatar_path) }}" alt="Avatar" />
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($user->roles as $role)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">{{ __('No Roles') }}</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider
                                    @if($user->status === 'active') bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-300
                                    @elseif($user->status === 'inactive') bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                    @else bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-300 @endif">
                                    {{ $user->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if($user->last_login_at)
                                    <div>{{ $user->last_login_at->diffForHumans() }}</div>
                                    <div class="text-xs text-gray-400">{{ $user->last_login_ip }}</div>
                                @else
                                    <span class="text-xs text-gray-400 italic">{{ __('Never') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right space-x-2">
                                @if($user->trashed())
                                    <button wire:click="restoreUser('{{ $user->id }}')" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 hover:underline cursor-pointer">
                                        {{ __('Restore') }}
                                    </button>
                                @else
                                    <button wire:click="openEditModal('{{ $user->id }}')" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:underline cursor-pointer">
                                        {{ __('Edit') }}
                                    </button>
                                    <button wire:click="viewLogs('{{ $user->id }}')" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:underline cursor-pointer">
                                        {{ __('Logs') }}
                                    </button>
                                    <button wire:click="deleteUser('{{ $user->id }}')" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 hover:underline cursor-pointer" onclick="confirm('Are you sure you want to delete this user?') || event.stopImmediatePropagation()">
                                        {{ __('Delete') }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                {{ __('No users found matching your filters.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/20 border-t border-gray-200 dark:border-gray-700">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- User Modal (Create / Edit) -->
    @if($showingUserModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Modal backdrop -->
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 transition-opacity" aria-hidden="true" wire:click="$set('showingUserModal', false)"></div>

                <!-- Center elements -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-700">
                    <form wire:submit="saveUser">
                        <div class="bg-white dark:bg-gray-800 px-6 py-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white" id="modal-title">
                                {{ $userId ? __('Edit ERP User') : __('Create New ERP User') }}
                            </h3>
                        </div>

                        <div class="bg-white dark:bg-gray-800 px-6 py-6 space-y-4">
                            <!-- Name -->
                            <div>
                                <x-input-label for="modal-name" :value="__('Full Name')" />
                                <x-text-input wire:model="name" id="modal-name" type="text" class="mt-1 block w-full" placeholder="e.g. Rahul Sharma" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-input-label for="modal-email" :value="__('Email Address')" />
                                <x-text-input wire:model="email" id="modal-email" type="email" class="mt-1 block w-full" placeholder="e.g. rahul@livingliquidz.com" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />
                            </div>

                            <!-- Password (Required for create, optional for edit) -->
                            <div>
                                <x-input-label for="modal-password" :value="__('Password')" />
                                <x-text-input wire:model="password" id="modal-password" type="password" class="mt-1 block w-full" placeholder="{{ $userId ? __('Leave blank to keep current password') : __('Choose secure password') }}" />
                                <x-input-error :messages="$errors->get('password')" class="mt-1" />
                            </div>

                            <!-- Status -->
                            <div>
                                <x-input-label for="modal-status" :value="__('Account Status')" />
                                <select wire:model="status" id="modal-status" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2">
                                    <option value="active">{{ __('Active') }}</option>
                                    <option value="inactive">{{ __('Inactive') }}</option>
                                    <option value="suspended">{{ __('Suspended') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-1" />
                            </div>

                            <!-- Roles (Checkbox List) -->
                            <div>
                                <x-input-label :value="__('Assign Roles (Select at least one)')" />
                                <div class="mt-2 grid grid-cols-2 gap-2 max-h-48 overflow-y-auto p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    @foreach($roles as $role)
                                        <label class="inline-flex items-center">
                                            <input wire:model="selectedRoles" type="checkbox" value="{{ $role->name }}" class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" />
                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-350">{{ $role->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('selectedRoles')" class="mt-1" />
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100 dark:border-gray-700">
                            <x-primary-button type="submit">
                                {{ __('Save') }}
                            </x-primary-button>
                            <x-secondary-button wire:click="$set('showingUserModal', false)">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Activity Logs Modal -->
    @if($showingLogsModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="logs-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Modal backdrop -->
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 transition-opacity" aria-hidden="true" wire:click="$set('showingLogsModal', false)"></div>

                <!-- Center elements -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100 dark:border-gray-700">
                    <div class="bg-white dark:bg-gray-800 px-6 py-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white" id="logs-modal-title">
                            {{ __('Activity Logs - ') }} {{ $logUserName }}
                        </h3>
                        <button wire:click="$set('showingLogsModal', false)" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bg-white dark:bg-gray-800 px-6 py-6 max-h-[500px] overflow-y-auto space-y-4">
                        @forelse($userLogs as $log)
                            <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg text-sm border border-gray-100 dark:border-gray-800">
                                <div class="flex justify-between items-start">
                                    <span class="font-semibold text-indigo-600 dark:text-indigo-400 capitalize">{{ $log['event'] ?? __('event') }}</span>
                                    <span class="text-xs text-gray-400">{{ $log['created_at'] }}</span>
                                </div>
                                <p class="mt-2 text-gray-800 dark:text-gray-200">{{ $log['description'] }}</p>
                                @if($log['properties'])
                                    <div class="mt-2 p-2 bg-white dark:bg-gray-950 rounded text-xs font-mono overflow-x-auto text-gray-600 dark:text-gray-400 border border-gray-100 dark:border-gray-900">
                                        {{ $log['properties'] }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-center text-sm text-gray-500 dark:text-gray-400 italic py-10">
                                {{ __('No activity logs found for this user.') }}
                            </p>
                        @endforelse
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse border-t border-gray-100 dark:border-gray-700">
                        <x-secondary-button wire:click="$set('showingLogsModal', false)">
                            {{ __('Close') }}
                        </x-secondary-button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
