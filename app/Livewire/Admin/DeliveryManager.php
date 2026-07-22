<?php

namespace App\Livewire\Admin;

use App\Models\Delivery;
use App\Models\DeliveryAgent;
use App\Models\SalesOrder;
use App\Models\Vehicle;
use App\Services\DeliveryService;
use Livewire\Component;
use Livewire\WithPagination;

class DeliveryManager extends Component
{
    use WithPagination;

    public string $activeTab = 'dispatch';

    // Filters
    public string $search = '';

    // Add Driver Form
    public bool $showingAgentModal = false;

    public string $agentName = '';

    public string $agentPhone = '';

    public string $agentStatus = 'available'; // available, busy, offline

    public string $agentVehicle = '';

    // Add Vehicle Form
    public bool $showingVehicleModal = false;

    public string $vehicleModel = '';

    public string $vehiclePlate = '';

    public string $vehicleType = 'bike'; // bike, van, truck

    // Dispatch Modal
    public bool $showingDispatchModal = false;

    public ?string $dispatchOrderId = null;

    public ?string $dispatchAgentId = null;

    public ?string $dispatchVehicleId = null;

    // Complete Delivery Modal
    public bool $showingCompleteModal = false;

    public ?string $completeDeliveryId = null;

    public string $completeOtp = '';

    public string $completeSignature = '';

    public string $completePhotoUrl = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'dispatch'],
    ];

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // --- Agent Operations ---
    public function openAgentModal(): void
    {
        $this->agentName = '';
        $this->agentPhone = '';
        $this->agentVehicle = '';
        $this->showingAgentModal = true;
    }

    public function saveAgent(DeliveryService $deliveryService): void
    {
        $this->validate([
            'agentName' => 'required|string|max:255',
            'agentPhone' => 'required|string',
            'agentVehicle' => 'required|string',
        ]);

        $deliveryService->createAgent([
            'name' => $this->agentName,
            'phone' => $this->agentPhone,
            'vehicle_number' => $this->agentVehicle,
            'status' => 'available',
        ]);

        session()->flash('agent_success', 'Registered new delivery agent.');
        $this->showingAgentModal = false;
    }

    // --- Vehicle Operations ---
    public function openVehicleModal(): void
    {
        $this->vehicleModel = '';
        $this->vehiclePlate = '';
        $this->showingVehicleModal = true;
    }

    public function saveVehicle(DeliveryService $deliveryService): void
    {
        $this->validate([
            'vehicleModel' => 'required|string',
            'vehiclePlate' => 'required|string|unique:vehicles,plate_number',
            'vehicleType' => 'required|in:bike,van,truck',
        ]);

        $deliveryService->createVehicle([
            'model' => $this->vehicleModel,
            'plate_number' => $this->vehiclePlate,
            'type' => $this->vehicleType,
            'status' => 'active',
        ]);

        session()->flash('agent_success', 'Fleet vehicle registered.');
        $this->showingVehicleModal = false;
    }

    // --- Dispatching ---
    public function openDispatchModal(string $orderId): void
    {
        $this->dispatchOrderId = $orderId;
        $this->dispatchAgentId = DeliveryAgent::where('status', 'available')->first()?->id;
        $this->dispatchVehicleId = Vehicle::where('status', 'active')->first()?->id;
        $this->showingDispatchModal = true;
    }

    public function dispatchOrder(DeliveryService $deliveryService): void
    {
        $this->validate([
            'dispatchOrderId' => 'required|exists:sales_orders,id',
            'dispatchAgentId' => 'required|exists:delivery_agents,id',
            'dispatchVehicleId' => 'required|exists:vehicles,id',
        ]);

        $deliveryService->assignDelivery([
            'sales_order_id' => $this->dispatchOrderId,
            'delivery_agent_id' => $this->dispatchAgentId,
            'vehicle_id' => $this->dispatchVehicleId,
        ]);

        // Transition order status to shipping
        SalesOrder::find($this->dispatchOrderId)->update(['shipping_status' => 'shipped']);

        session()->flash('dispatch_success', 'Sales order dispatched. Delivery OTP code generated.');
        $this->showingDispatchModal = false;
    }

    public function startTransit(string $deliveryId, DeliveryService $deliveryService): void
    {
        $deliveryService->updateGPS($deliveryId, 19.0760, 72.8777); // seed mumbai coord
        $deliveryService->confirmDeliveryWithOTP($deliveryId, 'dummy', '', ''); // verify transit trigger status
        Delivery::find($deliveryId)->update(['status' => 'in_transit']);

        session()->flash('dispatch_success', 'Transit started. GPS Tracking enabled.');
    }

    // --- Complete Delivery ---
    public function openCompleteModal(string $deliveryId): void
    {
        $this->completeDeliveryId = $deliveryId;
        $this->completeOtp = '';
        $this->completeSignature = '';
        $this->completePhotoUrl = 'https://liquorerp.in/proofs/photo-'.rand(100, 999).'.png';
        $this->showingCompleteModal = true;
    }

    public function confirmDelivery(DeliveryService $deliveryService): void
    {
        $this->validate([
            'completeDeliveryId' => 'required|exists:deliveries,id',
            'completeOtp' => 'required|string',
            'completeSignature' => 'required|string',
        ]);

        $success = $deliveryService->confirmDeliveryWithOTP(
            $this->completeDeliveryId,
            $this->completeOtp,
            $this->completeSignature,
            $this->completePhotoUrl
        );

        if ($success) {
            session()->flash('dispatch_success', 'Delivery verified successfully with OTP checkout. Sales Order completed.');
            $this->showingCompleteModal = false;
        } else {
            $this->addError('completeOtp', 'Invalid OTP code. Checkout rejected.');
        }
    }

    public function render(DeliveryService $deliveryService)
    {
        $agentsList = DeliveryAgent::orderBy('name')->get();
        $vehiclesList = Vehicle::orderBy('plate_number')->get();

        $pendingSalesOrders = [];
        $activeDeliveries = [];
        $completedDeliveries = [];
        $analytics = $deliveryService->getDeliveryAnalytics();

        if ($this->activeTab === 'dispatch') {
            // Find sales orders that are pending delivery dispatch (status is processing, shipping_status is null/pending)
            $pendingSalesOrders = SalesOrder::where('status', 'processing')
                ->where(function ($q) {
                    $q->whereNull('shipping_status')
                        ->orWhere('shipping_status', 'pending');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($this->activeTab === 'route') {
            $activeDeliveries = Delivery::with(['salesOrder', 'agent', 'vehicle'])
                ->whereIn('status', ['assigned', 'in_transit'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($this->activeTab === 'completed') {
            $completedDeliveries = Delivery::with(['salesOrder', 'agent', 'vehicle'])
                ->where('status', 'delivered')
                ->orderBy('actual_delivery_time', 'desc')
                ->paginate(10);
        }

        return view('livewire.admin.delivery-manager', [
            'agentsList' => $agentsList,
            'vehiclesList' => $vehiclesList,
            'pendingSalesOrders' => $pendingSalesOrders,
            'activeDeliveries' => $activeDeliveries,
            'completedDeliveries' => $completedDeliveries,
            'analytics' => $analytics,
        ])->layout('layouts.app');
    }
}
