<?php

namespace App\Livewire\Admin;

use App\Services\ReportService;
use Livewire\Component;

class ReportManager extends Component
{
    public string $activeTab = 'sales';

    public string $startDate = '';

    public string $endDate = '';

    protected $queryString = [
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'activeTab' => ['except' => 'sales'],
    ];

    public function mount(): void
    {
        if (empty($this->startDate)) {
            $this->startDate = date('Y-m-01'); // start of current month
        }
        if (empty($this->endDate)) {
            $this->endDate = date('Y-m-t'); // end of current month
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function exportCSV(): void
    {
        session()->flash('report_success', 'Report data successfully compiled and simulated CSV download dispatched.');
    }

    public function render(ReportService $reportService)
    {
        $reportData = $reportService->generateEnterpriseReport($this->startDate, $this->endDate);

        return view('livewire.admin.report-manager', [
            'report' => $reportData,
        ])->layout('layouts.app');
    }
}
