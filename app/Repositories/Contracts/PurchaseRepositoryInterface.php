<?php

namespace App\Repositories\Contracts;

use App\Models\GoodsReceiptNote;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use Illuminate\Pagination\LengthAwarePaginator;

interface PurchaseRepositoryInterface
{
    // Requisition Operations
    public function getRequisitions(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function findRequisition(string $id): ?PurchaseRequisition;

    public function createRequisition(array $data): PurchaseRequisition;

    public function updateRequisition(string $id, array $data): ?PurchaseRequisition;

    public function deleteRequisition(string $id): bool;

    // PO Operations
    public function getPurchaseOrders(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function findPurchaseOrder(string $id): ?PurchaseOrder;

    public function createPurchaseOrder(array $data): PurchaseOrder;

    public function updatePurchaseOrder(string $id, array $data): ?PurchaseOrder;

    // GRN Operations
    public function getGRNs(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function findGRN(string $id): ?GoodsReceiptNote;

    public function createGRN(array $data): GoodsReceiptNote;

    // Invoice Operations
    public function getInvoices(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function findInvoice(string $id): ?PurchaseInvoice;

    public function createInvoice(array $data): PurchaseInvoice;

    // Stats Dashboard Metrics
    public function getMetrics(): array;
}
