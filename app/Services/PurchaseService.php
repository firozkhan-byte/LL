<?php

namespace App\Services;

use App\Models\GoodsReceiptNote;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\Supplier;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class PurchaseService
{
    protected PurchaseRepositoryInterface $purchaseRepository;

    public function __construct(PurchaseRepositoryInterface $purchaseRepository)
    {
        $this->purchaseRepository = $purchaseRepository;
    }

    // Requisitions
    public function getRequisitions(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->purchaseRepository->getRequisitions($filters, $perPage);
    }

    public function getRequisition(string $id): ?PurchaseRequisition
    {
        return $this->purchaseRepository->findRequisition($id);
    }

    public function createRequisition(array $data): PurchaseRequisition
    {
        return $this->purchaseRepository->createRequisition($data);
    }

    public function updateRequisition(string $id, array $data): ?PurchaseRequisition
    {
        return $this->purchaseRepository->updateRequisition($id, $data);
    }

    public function deleteRequisition(string $id): bool
    {
        return $this->purchaseRepository->deleteRequisition($id);
    }

    // Purchase Orders
    public function getPurchaseOrders(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->purchaseRepository->getPurchaseOrders($filters, $perPage);
    }

    public function getPurchaseOrder(string $id): ?PurchaseOrder
    {
        return $this->purchaseRepository->findPurchaseOrder($id);
    }

    public function createPurchaseOrder(array $data): PurchaseOrder
    {
        return $this->purchaseRepository->createPurchaseOrder($data);
    }

    public function updatePurchaseOrder(string $id, array $data): ?PurchaseOrder
    {
        return $this->purchaseRepository->updatePurchaseOrder($id, $data);
    }

    // GRNs
    public function getGRNs(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->purchaseRepository->getGRNs($filters, $perPage);
    }

    public function getGRN(string $id): ?GoodsReceiptNote
    {
        return $this->purchaseRepository->findGRN($id);
    }

    public function createGRN(array $data): GoodsReceiptNote
    {
        // Mark PO status as received (or partially_received) when GRN is filed
        $grn = $this->purchaseRepository->createGRN($data);

        $po = PurchaseOrder::find($grn->purchase_order_id);
        if ($po) {
            $po->update(['status' => 'received']);
        }

        return $grn;
    }

    // Invoices
    public function getInvoices(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->purchaseRepository->getInvoices($filters, $perPage);
    }

    public function getInvoice(string $id): ?PurchaseInvoice
    {
        return $this->purchaseRepository->findInvoice($id);
    }

    public function createInvoice(array $data): PurchaseInvoice
    {
        $invoice = $this->purchaseRepository->createInvoice($data);

        // Update supplier outstanding balance automatically
        $supplier = Supplier::find($invoice->supplier_id);
        if ($supplier) {
            $supplier->increment('outstanding_balance', $invoice->total_amount);
        }

        return $invoice;
    }

    // Dashboard analytics
    public function getStats(): array
    {
        return $this->purchaseRepository->getMetrics();
    }
}
