<?php

namespace App\Services;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierService
{
    protected SupplierRepositoryInterface $supplierRepository;

    protected ApprovalService $approvalService;

    public function __construct(SupplierRepositoryInterface $supplierRepository, ApprovalService $approvalService)
    {
        $this->supplierRepository = $supplierRepository;
        $this->approvalService = $approvalService;
    }

    public function getSuppliers(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->supplierRepository->searchAndFilter($filters, $perPage);
    }

    public function getSupplier(string $id): ?Supplier
    {
        return $this->supplierRepository->find($id);
    }

    public function proposeSupplier(array $data, string $requestedBy)
    {
        // Enforce pending_approval status upon draft proposal
        $data['status'] = 'pending_approval';

        return $this->approvalService->proposeChange(
            Supplier::class,
            'create',
            $data,
            $requestedBy
        );
    }

    public function proposeUpdate(string $id, array $data, string $requestedBy)
    {
        $data['id'] = $id;

        return $this->approvalService->proposeChange(
            Supplier::class,
            'update',
            $data,
            $requestedBy
        );
    }

    public function proposeDelete(string $id, string $requestedBy)
    {
        return $this->approvalService->proposeChange(
            Supplier::class,
            'delete',
            ['id' => $id],
            $requestedBy
        );
    }

    public function createSupplier(array $data): Supplier
    {
        return $this->supplierRepository->create($data);
    }

    public function updateSupplier(string $id, array $data): ?Supplier
    {
        return $this->supplierRepository->update($id, $data);
    }

    public function deleteSupplier(string $id): bool
    {
        return $this->supplierRepository->delete($id);
    }

    public function restoreSupplier(string $id): bool
    {
        return $this->supplierRepository->restore($id);
    }

    public function getStats(): array
    {
        return $this->supplierRepository->getMetrics();
    }
}
