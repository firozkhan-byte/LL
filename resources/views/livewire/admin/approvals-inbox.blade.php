<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            {{ __('Approvals Inbox') }}
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ __('Review and authorize pending modifications for branches, stores, and warehouses.') }}
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

    <!-- Inbox List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-5 border-b dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Pending Authorizations') }}</h3>
            <span class="px-2.5 py-1 text-xs font-bold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 rounded-full">
                {{ count($approvals) }} {{ __('Pending') }}
            </span>
        </div>

        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($approvals as $app)
                <div class="p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                @if($app->action === 'create') bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300
                                @elseif($app->action === 'update') bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-300
                                @else bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-300 @endif">
                                {{ $app->action }}
                            </span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ class_basename($app->approvable_type) }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Requested by <span class="font-medium text-gray-700 dark:text-gray-300">{{ $app->requester->name }}</span>
                            &bull; {{ $app->created_at->diffForHumans() }}
                        </div>
                        <div class="text-xs font-mono text-gray-400">
                            Payload: {{ Str::limit(json_encode($app->data), 70) }}
                        </div>
                    </div>

                    <div class="mt-4 sm:mt-0">
                        <button wire:click="openReviewModal('{{ $app->id }}')" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow cursor-pointer">
                            {{ __('Review Request') }}
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-sm text-gray-500 italic">
                    {{ __('No pending approvals inside inbox. Rest easy!') }}
                </div>
            @endforelse
        </div>
    </div>

    <!-- Review Modal -->
    @if($showingReviewModal && $selectedApproval)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="$set('showingReviewModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border dark:border-gray-700">
                    <div class="px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ __('Review Request Details') }}
                        </h3>
                        <button wire:click="$set('showingReviewModal', false)" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Request Metadata Info -->
                        <div class="grid grid-cols-2 gap-4 text-sm bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border dark:border-gray-800">
                            <div>
                                <span class="block text-xs text-gray-400 font-semibold uppercase tracking-wider">{{ __('Entity Type') }}</span>
                                <span class="font-bold text-gray-800 dark:text-gray-200">{{ class_basename($selectedApproval->approvable_type) }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 font-semibold uppercase tracking-wider">{{ __('Action Type') }}</span>
                                <span class="font-bold text-gray-800 dark:text-gray-200 capitalize">{{ $selectedApproval->action }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 font-semibold uppercase tracking-wider">{{ __('Requested By') }}</span>
                                <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $selectedApproval->requester->name }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 font-semibold uppercase tracking-wider">{{ __('Request Date') }}</span>
                                <span class="text-gray-600 dark:text-gray-300">{{ $selectedApproval->created_at->format('Y-m-d H:i:s') }}</span>
                            </div>
                        </div>

                        <!-- Diff/Data Details -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">{{ __('Proposed Changes / Parameters') }}</h4>
                            <div class="border rounded-lg overflow-hidden border-gray-200 dark:border-gray-700">
                                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 text-left">
                                    <thead class="bg-gray-50 dark:bg-gray-900/30">
                                        <tr>
                                            <th class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Field Parameter') }}</th>
                                            <th class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Proposed Value') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                                        @foreach($selectedApproval->data as $key => $val)
                                            <tr>
                                                <td class="px-4 py-2 font-mono text-xs text-indigo-600 dark:text-indigo-400">{{ $key }}</td>
                                                <td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-200">{{ is_array($val) ? json_encode($val) : $val }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Rejection Input -->
                        <div class="space-y-2 border-t pt-4 dark:border-gray-700">
                            <x-input-label for="rejectionReason" :value="__('Rejection Reason (Required only when rejecting)')" />
                            <textarea wire:model="rejectionReason" id="rejectionReason" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-red-500 focus:border-red-500 text-sm py-2" placeholder="e.g. Code format mismatch or branch details missing..."></textarea>
                            <x-input-error :messages="$errors->get('rejectionReason')" />
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex justify-between border-t dark:border-gray-700">
                        <x-danger-button wire:click="rejectRequest">{{ __('Reject Change') }}</x-danger-button>
                        <div class="flex items-center space-x-2">
                            <x-secondary-button wire:click="$set('showingReviewModal', false)">{{ __('Close') }}</x-secondary-button>
                            <x-primary-button wire:click="approveRequest">{{ __('Approve & Save') }}</x-primary-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
