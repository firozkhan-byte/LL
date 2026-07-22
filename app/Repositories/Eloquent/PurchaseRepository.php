<?php

namespace App\Repositories\Eloquent;

use App\Models\GoodsReceiptNote;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class PurchaseRepository implements PurchaseRepositoryInterface
{
    public function getRequisitions(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = PurchaseRequisition::with(['requester', 'items.product']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['search'])) {
            $query->where('code', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findRequisition(string $id): ?PurchaseRequisition
    {
        return PurchaseRequisition::with(['requester', 'items.product'])->find($id);
    }

    public function createRequisition(array $data): PurchaseRequisition
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $req = PurchaseRequisition::create($data);
        foreach ($items as $item) {
            $req->items()->create($item);
        }

        return $req;
    }

    public function updateRequisition(string $id, array $data): ?PurchaseRequisition
    {
        $req = PurchaseRequisition::find($id);
        if (! $req) {
            return null;
        }

        $items = $data['items'] ?? null;
        unset($data['items']);

        $req->update($data);

        if ($items !== null) {
            $req->items()->delete();
            foreach ($items as $item) {
                $req->items()->create($item);
            }
        }

        return $req;
    }

    public function deleteRequisition(string $id): bool
    {
        $req = PurchaseRequisition::find($id);

        return $req ? $req->delete() : false;
    }

    public function getPurchaseOrders(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = PurchaseOrder::with(['supplier', 'items.product']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['search'])) {
            $query->where('code', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findPurchaseOrder(string $id): ?PurchaseOrder
    {
        return PurchaseOrder::with(['supplier', 'items.product'])->find($id);
    }

    public function createPurchaseOrder(array $data): PurchaseOrder
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $po = PurchaseOrder::create($data);
        foreach ($items as $item) {
            $po->items()->create($item);
        }

        return $po;
    }

    public function updatePurchaseOrder(string $id, array $data): ?PurchaseOrder
    {
        $po = PurchaseOrder::find($id);
        if (! $po) {
            return null;
        }

        $items = $data['items'] ?? null;
        unset($data['items']);

        $po->update($data);

        if ($items !== null) {
            $po->items()->delete();
            foreach ($items as $item) {
                $po->items()->create($item);
            }
        }

        return $po;
    }

    public function getGRNs(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = GoodsReceiptNote::with(['purchaseOrder', 'receiver', 'items.product']);

        if (! empty($filters['search'])) {
            $query->where('code', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findGRN(string $id): ?GoodsReceiptNote
    {
        return GoodsReceiptNote::with(['purchaseOrder', 'receiver', 'items.product'])->find($id);
    }

    public function createGRN(array $data): GoodsReceiptNote
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $grn = GoodsReceiptNote::create($data);
        foreach ($items as $item) {
            $grn->items()->create($item);
        }

        return $grn;
    }

    public function getInvoices(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = PurchaseInvoice::with(['supplier', 'grn', 'items.product']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['search'])) {
            $query->where('code', 'like', "%{$filters['search']}%")
                ->orWhere('invoice_number', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findInvoice(string $id): ?PurchaseInvoice
    {
        return PurchaseInvoice::with(['supplier', 'grn', 'items.product'])->find($id);
    }

    public function createInvoice(array $data): PurchaseInvoice
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $inv = PurchaseInvoice::create($data);
        foreach ($items as $item) {
            $inv->items()->create($item);
        }

        return $inv;
    }

    public function getMetrics(): array
    {
        return [
            'total_orders' => PurchaseOrder::count(),
            'pending_receipts' => PurchaseOrder::whereNotIn('status', ['received', 'cancelled'])->count(),
            'total_spent' => PurchaseOrder::where('status', 'approved')->sum('total_amount'),
            'overdue_invoices' => PurchaseInvoice::where('status', 'unpaid')->where('due_date', '<', now()->toDateString())->count(),
        ];
    }
}
