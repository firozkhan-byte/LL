<?php

namespace App\Repositories\Eloquent;

use App\Models\Approval;
use App\Repositories\Contracts\ApprovalRepositoryInterface;
use Illuminate\Support\Collection;

class ApprovalRepository implements ApprovalRepositoryInterface
{
    public function allPending(): Collection
    {
        return Approval::with(['requester', 'approver'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function find(string $id): ?Approval
    {
        return Approval::with(['requester', 'approver'])->find($id);
    }

    public function create(array $data): Approval
    {
        return Approval::create($data);
    }

    public function updateStatus(string $id, string $status, ?string $approverId, ?string $rejectionReason = null): ?Approval
    {
        $approval = Approval::find($id);
        if ($approval) {
            $approval->update([
                'status' => $status,
                'approved_by' => $approverId,
                'rejection_reason' => $rejectionReason,
            ]);

            return $approval;
        }

        return null;
    }
}
