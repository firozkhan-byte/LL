<?php

namespace App\Livewire\Admin;

use App\Services\CompanyService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class CompanyTree extends Component
{
    public function mount(): void
    {
        abort_if(Gate::denies('manage-company'), 403);
    }

    public function render(CompanyService $companyService)
    {
        $companies = $companyService->getTreeHierarchy();

        return view('livewire.admin.company-tree', [
            'companies' => $companies,
        ])->layout('layouts.app');
    }
}
