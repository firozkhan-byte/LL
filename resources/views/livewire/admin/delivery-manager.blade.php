<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ __('Transit & Delivery Management Hub') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Assign orders to available delivery boys, allocate fleet vehicles, verify checkouts with OTP delivery, and register Proof of Delivery.') }}
            </p>
        </div>
        <div class="flex gap-2">
            <button wire:click="openAgentModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                {{ __('Add Agent') }}
            </button>
            <button wire:click="openVehicleModal" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10M13 16h6l1.724-3.448A1 1 0 0019.829 12H13v4z" />
                </svg>
                {{ __('Add Vehicle') }}
            </button>
        </div>
    </div>

    <!-- Alert messages -->
    @if (session()->has('agent_success'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-lg flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('agent_success') }}</span>
        </div>
    @endif
    @if (session()->has('dispatch_success'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-lg flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('dispatch_success') }}</span>
        </div>
    @endif

    <!-- Analytics Dashboard widgets -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Assigned Orders</span>
            <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-2">{{ $analytics['assigned_count'] }} Orders</h3>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">In Transit</span>
            <h3 class="text-3xl font-black text-indigo-600 dark:text-indigo-400 font-mono mt-2">{{ $analytics['transit_count'] }} Drivers</h3>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Deliveries Completed</span>
            <h3 class="text-3xl font-black text-green-600 mt-2">{{ $analytics['completed_count'] }} Orders</h3>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Drivers On-Duty</span>
            <h3 class="text-3xl font-black text-indigo-650 dark:text-indigo-400 font-mono mt-2">{{ $analytics['active_agents'] }} Available</h3>
        </div>
    </div>

    <!-- Tabs Nav -->
    <div class="border-b border-gray-200 dark:border-gray-700 flex space-x-8 overflow-x-auto">
        <button wire:click="setTab('dispatch')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'dispatch' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Dispatch Console') }}
        </button>
        <button wire:click="setTab('fleet')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'fleet' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Fleet & Drivers') }}
        </button>
        <button wire:click="setTab('route')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'route' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Route & GPS tracking') }}
        </button>
        <button wire:click="setTab('completed')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'completed' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Proof of Delivery (POD) logs') }}
        </button>
    </div>

    <!-- Main Tab Workspace -->
    <div class="space-y-6">
        @if ($activeTab === 'dispatch')
            <!-- Pending Sales Orders to Dispatch Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Order Number</th>
                                <th class="px-6 py-3">Customer Name</th>
                                <th class="px-6 py-3 text-right">Items Count</th>
                                <th class="px-6 py-3 text-right">Order Value</th>
                                <th class="px-6 py-3 text-center">Shipping Status</th>
                                <th class="px-6 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($pendingSalesOrders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors text-sm">
                                    <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $order->order_number }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $order->customer->name }}</td>
                                    <td class="px-6 py-4 text-right font-mono text-gray-700 dark:text-gray-300">{{ $order->items->count() }}</td>
                                    <td class="px-6 py-4 text-right font-mono font-bold text-gray-950 dark:text-white">₹{{ number_format($order->total_amount, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400">
                                            {{ ucfirst($order->shipping_status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button wire:click="openDispatchModal('{{ $order->id }}')" class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 font-semibold rounded cursor-pointer transition shadow">
                                            Dispatch Order
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No pending sales orders requiring delivery dispatch.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div>
                {{ $pendingSalesOrders->links() }}
            </div>
        @endif

        @if ($activeTab === 'fleet')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Delivery agents directory -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-950 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">Available Delivery Boys</h3>
                    <div class="divide-y divide-gray-150 dark:divide-gray-700">
                        @foreach ($agentsList as $agent)
                            <div class="py-3 flex justify-between items-center text-sm">
                                <div>
                                    <span class="font-bold text-gray-950 dark:text-white block">{{ $agent->name }}</span>
                                    <span class="text-xs text-gray-500 block">Phone: {{ $agent->phone }} | Vehicle: {{ $agent->vehicle_number }}</span>
                                </div>
                                <span class="px-2.5 py-0.5 text-xs font-bold uppercase rounded-full
                                    {{ $agent->status === 'available' ? 'bg-green-105 text-green-700 dark:text-green-400' : 'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-400' }}
                                ">
                                    {{ $agent->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Vehicles fleet logs -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-950 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">Fleet Vehicles</h3>
                    <div class="divide-y divide-gray-150 dark:divide-gray-700">
                        @foreach ($vehiclesList as $veh)
                            <div class="py-3 flex justify-between items-center text-sm">
                                <div>
                                    <span class="font-bold text-gray-950 dark:text-white block">{{ $veh->model }}</span>
                                    <span class="text-xs text-gray-500 block">Plate #: {{ $veh->plate_number }} | Type: {{ strtoupper($veh->type) }}</span>
                                </div>
                                <span class="px-2.5 py-0.5 text-xs font-bold uppercase rounded-full bg-slate-105 text-slate-700 dark:text-slate-200">
                                    {{ $veh->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @if ($activeTab === 'route')
            <!-- GPS timeline tracking list -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Order Number</th>
                                <th class="px-6 py-3">Assigned Driver</th>
                                <th class="px-6 py-3 text-center">GPS Coordinates</th>
                                <th class="px-6 py-3 text-center">Checkout OTP</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-center">GPS Simulate Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($activeDeliveries as $del)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors text-sm">
                                    <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $del->salesOrder->order_number }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $del->agent->name }}</td>
                                    <td class="px-6 py-4 text-center font-mono text-xs">
                                        @if ($del->gps_lat)
                                            <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $del->gps_lat }}, {{ $del->gps_lng }}</span>
                                        @else
                                            <span class="text-gray-400 italic">No GPS signal</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-bold text-gray-900 dark:text-white bg-slate-100/50 dark:bg-slate-700/30">{{ $del->otp }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 text-xs font-bold uppercase rounded-full bg-blue-100 text-blue-700 dark:text-blue-400">
                                            {{ $del->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-1.5">
                                            @if ($del->status === 'assigned')
                                                <button wire:click="startTransit('{{ $del->id }}')" class="text-xs bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 px-2.5 py-1 font-bold rounded cursor-pointer transition">
                                                    Start Transit
                                                </button>
                                            @elseif ($del->status === 'in_transit')
                                                <button wire:click="openCompleteModal('{{ $del->id }}')" class="text-xs bg-green-105 hover:bg-green-205 dark:bg-green-950/45 text-green-700 dark:text-green-400 px-2.5 py-1 font-bold rounded cursor-pointer transition">
                                                    OTP Verify & Checkout
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No active deliveries currently in transit.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div>
                {{ $activeDeliveries->links() }}
            </div>
        @endif

        @if ($activeTab === 'completed')
            <!-- Completed Deliveries Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Order Number</th>
                                <th class="px-6 py-3">Completed Agent</th>
                                <th class="px-6 py-3">Actual Payout Time</th>
                                <th class="px-6 py-3">Proof Signature (POD)</th>
                                <th class="px-6 py-3">Photo URL</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($completedDeliveries as $del)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors text-sm">
                                    <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $del->salesOrder->order_number }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $del->agent->name }}</td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400 font-mono">{{ $del->actual_delivery_time ? $del->actual_delivery_time->format('Y-m-d H:i') : '-' }}</td>
                                    <td class="px-6 py-4 text-xs italic font-semibold text-gray-800 dark:text-slate-200">{{ $del->proof_signature }}</td>
                                    <td class="px-6 py-4 text-xs font-mono text-indigo-500 underline select-all">{{ $del->proof_photo_url }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-105 text-green-700 dark:text-green-400">
                                            {{ ucfirst($del->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No completed deliveries on file.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div>
                {{ $completedDeliveries->links() }}
            </div>
        @endif
    </div>

    <!-- REGISTER AGENT MODAL -->
    @if ($showingAgentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-md rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Register Delivery Agent</h3>
                    <button wire:click="$set('showingAgentModal', false)" class="text-gray-400 hover:text-gray-650 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Driver Name</label>
                        <input type="text" wire:model="agentName" placeholder="Driver Full Name" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        @error('agentName') <p class="text-xs text-red-650 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Driver Contact Phone</label>
                        <input type="text" wire:model="agentPhone" placeholder="Mobile Number" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        @error('agentPhone') <p class="text-xs text-red-650 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Vehicle Plate Number</label>
                        <input type="text" wire:model="agentVehicle" placeholder="e.g. MH-12-AB-9999" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        @error('agentVehicle') <p class="text-xs text-red-650 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showingAgentModal', false)" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg cursor-pointer">Cancel</button>
                    <button type="button" wire:click="saveAgent" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg cursor-pointer transition-colors shadow">Register Driver</button>
                </div>
            </div>
        </div>
    @endif

    <!-- ADD FLEET VEHICLE MODAL -->
    @if ($showingVehicleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-md rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Register Fleet Vehicle</h3>
                    <button wire:click="$set('showingVehicleModal', false)" class="text-gray-400 hover:text-gray-650 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Vehicle Model</label>
                        <input type="text" wire:model="vehicleModel" placeholder="e.g. Hero Splendor, Tata Ace" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        @error('vehicleModel') <p class="text-xs text-red-650 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Plate Number</label>
                        <input type="text" wire:model="vehiclePlate" placeholder="e.g. MH-12-CD-5678" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        @error('vehiclePlate') <p class="text-xs text-red-650 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Vehicle Type</label>
                        <select wire:model="vehicleType" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            <option value="bike">Bike</option>
                            <option value="van">Van</option>
                            <option value="truck">Truck</option>
                        </select>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showingVehicleModal', false)" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg cursor-pointer">Cancel</button>
                    <button type="button" wire:click="saveVehicle" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg cursor-pointer transition-colors shadow">Save Vehicle</button>
                </div>
            </div>
        </div>
    @endif

    <!-- DISPATCH ORDER MODAL -->
    @if ($showingDispatchModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-md rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Dispatch Order assignment</h3>
                    <button wire:click="$set('showingDispatchModal', false)" class="text-gray-400 hover:text-gray-650 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Assign Driver</label>
                        <select wire:model="dispatchAgentId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            @foreach ($agentsList as $agent)
                                @if ($agent->status === 'available')
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Assign Vehicle</label>
                        <select wire:model="dispatchVehicleId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            @foreach ($vehiclesList as $veh)
                                @if ($veh->status === 'active')
                                    <option value="{{ $veh->id }}">{{ $veh->model }} ({{ $veh->plate_number }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showingDispatchModal', false)" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg cursor-pointer">Cancel</button>
                    <button type="button" wire:click="dispatchOrder" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg cursor-pointer transition-colors shadow">Dispatch assignment</button>
                </div>
            </div>
        </div>
    @endif

    <!-- OTP VERIFY & CHECKOUT MODAL -->
    @if ($showingCompleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-md rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">OTP Verification & POD Checkout</h3>
                    <button wire:click="$set('showingCompleteModal', false)" class="text-gray-400 hover:text-gray-650 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Secure 4-Digit Delivery OTP</label>
                        <input type="text" wire:model="completeOtp" placeholder="Enter OTP code" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-950 dark:text-gray-100 text-sm py-2 px-3 text-center tracking-widest font-bold">
                        @error('completeOtp') <p class="text-xs text-red-650 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Customer Signature Text (POD)</label>
                        <input type="text" wire:model="completeSignature" placeholder="Enter recipient full name" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        @error('completeSignature') <p class="text-xs text-red-650 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showingCompleteModal', false)" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg cursor-pointer">Cancel</button>
                    <button type="button" wire:click="confirmDelivery" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg cursor-pointer transition-colors shadow">Verify & Complete</button>
                </div>
            </div>
        </div>
    @endif
</div>
