<?php

namespace App\Livewire\Admin;

use App\Models\Branch;
use App\Models\SalesOrder;
use App\Services\AIService;
use Livewire\Component;

class AIManager extends Component
{
    public string $activeTab = 'dashboard';

    // AI Chat Assistant
    public string $chatInput = '';

    public array $chatHistory = [];

    protected $queryString = [
        'activeTab' => ['except' => 'dashboard'],
    ];

    public function mount(): void
    {
        $this->chatHistory[] = [
            'sender' => 'assistant',
            'message' => 'Hello! I am your AI Business Intelligence assistant. How can I help you analyze sales forecasts, inventory predictions, or purchase suggestions today?',
            'timestamp' => now()->format('H:i'),
        ];
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function sendChatMessage(AIService $aiService): void
    {
        if (empty(trim($this->chatInput))) {
            return;
        }

        $userMsg = $this->chatInput;
        $this->chatHistory[] = [
            'sender' => 'user',
            'message' => $userMsg,
            'timestamp' => now()->format('H:i'),
        ];

        // Process query
        $reply = $aiService->processChatQuery($userMsg);

        $this->chatHistory[] = [
            'sender' => 'assistant',
            'message' => $reply,
            'timestamp' => now()->format('H:i'),
        ];

        $this->chatInput = '';
    }

    public function render(AIService $aiService)
    {
        $salesForecast = $aiService->getSalesForecast();
        $purchaseSuggestions = $aiService->getPurchaseSuggestions();
        $customerSegmentation = $aiService->getCustomerSegmentation();

        // Branch rank list
        $branchesList = Branch::all();
        $branchPerformances = [];
        foreach ($branchesList as $br) {
            $sum = SalesOrder::where('warehouse_id', function ($q) use ($br) {
                $q->select('id')
                    ->from('warehouses')
                    ->where('branch_id', $br->id)
                    ->limit(1);
            })->sum('total_amount');

            $branchPerformances[] = [
                'name' => $br->name,
                'code' => $br->code,
                'sales_sum' => $sum,
            ];
        }

        return view('livewire.admin.ai-manager', [
            'forecast' => $salesForecast,
            'suggestions' => $purchaseSuggestions,
            'segments' => $customerSegmentation,
            'branchPerformances' => $branchPerformances,
        ])->layout('layouts.app');
    }
}
