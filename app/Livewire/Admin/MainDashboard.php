<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\BinInventory;
use App\Models\CrmTicket;
use App\Models\Delivery;
use App\Models\Employee;
use App\Models\ExciseLicense;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Services\AIService;
use Livewire\Component;

class MainDashboard extends Component
{
    // Mini AI Chat Box
    public string $chatInput = '';

    public array $chatHistory = [];

    public function mount(): void
    {
        $this->chatHistory[] = [
            'sender' => 'assistant',
            'message' => 'Hello! Ask me anything about Sales, Restock Suggestions, or HR rosters directly from here.',
        ];
    }

    public function askAI(AIService $aiService): void
    {
        if (empty(trim($this->chatInput))) {
            return;
        }

        $userMsg = $this->chatInput;
        $this->chatHistory[] = [
            'sender' => 'user',
            'message' => $userMsg,
        ];

        $reply = $aiService->processChatQuery($userMsg);

        $this->chatHistory[] = [
            'sender' => 'assistant',
            'message' => $reply,
        ];

        $this->chatInput = '';
    }

    public function render()
    {
        // 1. Sales & POS
        $salesRevenue = SalesOrder::sum('total_amount') ?: 0.00;
        $pendingSales = SalesOrder::where('status', 'processing')->count();

        // 2. Purchases
        $purchaseOutlay = PurchaseOrder::sum('total_amount') ?: 0.00;

        // 3. Inventory
        $totalStockCount = BinInventory::sum('quantity') ?: 0;

        // 4. Compliance
        $expiringLicenses = ExciseLicense::where('status', 'active')
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->count();

        // 5. HRMS
        $staffCount = Employee::where('status', 'active')->count();
        $presentToday = Attendance::whereDate('date', now())
            ->whereIn('status', ['present', 'late'])
            ->count();
        $attendanceRate = $staffCount > 0 ? round(($presentToday / $staffCount) * 100, 1) : 100.0;

        // 6. Deliveries in Transit
        $activeDeliveries = Delivery::with(['salesOrder', 'agent'])
            ->whereIn('status', ['assigned', 'in_transit'])
            ->limit(5)
            ->get();

        // 7. CRM Support tickets
        $pendingTickets = CrmTicket::where('status', 'open')->count();

        // Recent orders
        $recentOrders = SalesOrder::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.admin.main-dashboard', [
            'salesRevenue' => $salesRevenue,
            'pendingSales' => $pendingSales,
            'purchaseOutlay' => $purchaseOutlay,
            'totalStockCount' => $totalStockCount,
            'expiringLicenses' => $expiringLicenses,
            'staffCount' => $staffCount,
            'attendanceRate' => $attendanceRate,
            'activeDeliveries' => $activeDeliveries,
            'pendingTickets' => $pendingTickets,
            'recentOrders' => $recentOrders,
        ]);
    }
}
