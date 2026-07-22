<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\Branch;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Repositories\Contracts\ApprovalRepositoryInterface;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    protected ApprovalRepositoryInterface $approvalRepository;

    public function __construct(ApprovalRepositoryInterface $approvalRepository)
    {
        $this->approvalRepository = $approvalRepository;
    }

    public function getPendingApprovals()
    {
        return $this->approvalRepository->allPending();
    }

    public function findApproval(string $id): ?Approval
    {
        return $this->approvalRepository->find($id);
    }

    /**
     * Propose a creation or modification change that requires authorization.
     */
    public function proposeChange(string $type, string $action, array $data, string $requestedBy): Approval
    {
        $approvableId = $data['id'] ?? null;
        if (isset($data['id'])) {
            unset($data['id']);
        }

        // Force draft status for branch/store/warehouse if creating
        if ($action === 'create') {
            $data['status'] = 'pending_approval';
        }

        return $this->approvalRepository->create([
            'approvable_type' => $type,
            'approvable_id' => $approvableId,
            'action' => $action,
            'data' => $data,
            'status' => 'pending',
            'requested_by' => $requestedBy,
        ]);
    }

    /**
     * Approve the proposed change and apply changes to database.
     */
    public function approve(string $approvalId, string $approvedBy): bool
    {
        return DB::transaction(function () use ($approvalId, $approvedBy) {
            $approval = $this->approvalRepository->find($approvalId);
            if (! $approval || $approval->status !== 'pending') {
                return false;
            }

            $type = $approval->approvable_type;
            $action = $approval->action;
            $data = $approval->data;

            // Mark model active upon approval
            if ($action === 'create') {
                $data['status'] = 'active';

                $createdModel = match ($type) {
                    Branch::class => Branch::create($data),
                    Store::class => Store::create($data),
                    Warehouse::class => Warehouse::create($data),
                    Supplier::class => resolve(SupplierRepositoryInterface::class)->create($data),
                    default => throw new \InvalidArgumentException("Unknown type: {$type}"),
                };

                $approval->update([
                    'status' => 'approved',
                    'approved_by' => $approvedBy,
                    'approvable_id' => $createdModel->id,
                ]);
            } elseif ($action === 'update') {
                $model = match ($type) {
                    Branch::class => Branch::find($approval->approvable_id),
                    Store::class => Store::find($approval->approvable_id),
                    Warehouse::class => Warehouse::find($approval->approvable_id),
                    Supplier::class => resolve(SupplierRepositoryInterface::class)->update($approval->approvable_id, $data),
                    default => throw new \InvalidArgumentException("Unknown type: {$type}"),
                };

                if ($model && $type !== Supplier::class) {
                    $model->update($data);
                }

                $approval->update([
                    'status' => 'approved',
                    'approved_by' => $approvedBy,
                ]);
            } elseif ($action === 'delete') {
                $model = match ($type) {
                    Branch::class => Branch::find($approval->approvable_id),
                    Store::class => Store::find($approval->approvable_id),
                    Warehouse::class => Warehouse::find($approval->approvable_id),
                    Supplier::class => resolve(SupplierRepositoryInterface::class)->delete($approval->approvable_id),
                    default => throw new \InvalidArgumentException("Unknown type: {$type}"),
                };

                if ($model && $type !== Supplier::class) {
                    $model->delete();
                }

                $approval->update([
                    'status' => 'approved',
                    'approved_by' => $approvedBy,
                ]);
            }

            return true;
        });
    }

    /**
     * Reject the proposed change.
     */
    public function reject(string $approvalId, string $approvedBy, string $rejectionReason): bool
    {
        $approval = $this->approvalRepository->updateStatus($approvalId, 'rejected', $approvedBy, $rejectionReason);

        return $approval !== null;
    }
}
