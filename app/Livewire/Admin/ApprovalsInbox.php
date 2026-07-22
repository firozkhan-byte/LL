<?php

namespace App\Livewire\Admin;

use App\Services\ApprovalService;
use Livewire\Component;

class ApprovalsInbox extends Component
{
    public bool $showingReviewModal = false;

    public ?string $selectedApprovalId = null;

    public string $rejectionReason = '';

    public function mount(): void
    {
        // CEO, Finance Manager or Super Admin should access approvals
        abort_if(! auth()->user()->hasAnyRole(['Super Admin', 'CEO', 'Finance Manager']), 403);
    }

    public function render(ApprovalService $approvalService)
    {
        $approvals = $approvalService->getPendingApprovals();
        $selectedApproval = $this->selectedApprovalId ? $approvalService->findApproval($this->selectedApprovalId) : null;

        return view('livewire.admin.approvals-inbox', [
            'approvals' => $approvals,
            'selectedApproval' => $selectedApproval,
        ])->layout('layouts.app');
    }

    public function openReviewModal(string $id): void
    {
        $this->selectedApprovalId = $id;
        $this->rejectionReason = '';
        $this->showingReviewModal = true;
    }

    public function approveRequest(ApprovalService $approvalService): void
    {
        if ($this->selectedApprovalId) {
            $approvalService->approve($this->selectedApprovalId, auth()->id());
            session()->flash('message', 'Request approved and applied successfully.');
        }

        $this->showingReviewModal = false;
        $this->selectedApprovalId = null;
    }

    public function rejectRequest(ApprovalService $approvalService): void
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:5|max:500',
        ]);

        if ($this->selectedApprovalId) {
            $approvalService->reject($this->selectedApprovalId, auth()->id(), $this->rejectionReason);
            session()->flash('message', 'Request rejected.');
        }

        $this->showingReviewModal = false;
        $this->selectedApprovalId = null;
        $this->rejectionReason = '';
    }
}
