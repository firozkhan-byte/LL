<?php

namespace App\Repositories\Eloquent;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierRepository implements SupplierRepositoryInterface
{
    public function searchAndFilter(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Supplier::with(['contacts', 'bankAccounts', 'documents']);

        // Soft deletes state filter
        if (($filters['status'] ?? null) === 'deleted') {
            $query->onlyTrashed();
        } else {
            if (! empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }
        }

        // Search text (Name, Code, GSTIN, PAN)
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('gstin', 'like', "%{$search}%")
                    ->orWhere('pan', 'like', "%{$search}%");
            });
        }

        // Rating Filter
        if (! empty($filters['rating'])) {
            $query->where('rating', '>=', $filters['rating']);
        }

        // Credit Days Filter
        if (! empty($filters['credit_days'])) {
            $query->where('payment_terms_days', '<=', $filters['credit_days']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function find(string $id): ?Supplier
    {
        return Supplier::with(['contacts', 'bankAccounts', 'documents'])->find($id);
    }

    public function create(array $data): Supplier
    {
        $contacts = $data['contacts'] ?? [];
        $bankAccounts = $data['bank_accounts'] ?? [];
        $documents = $data['documents'] ?? [];

        unset($data['contacts'], $data['bank_accounts'], $data['documents']);

        $supplier = Supplier::create($data);

        foreach ($contacts as $contact) {
            $supplier->contacts()->create($contact);
        }

        foreach ($bankAccounts as $bank) {
            $supplier->bankAccounts()->create($bank);
        }

        foreach ($documents as $doc) {
            $supplier->documents()->create($doc);
        }

        return $supplier;
    }

    public function update(string $id, array $data): ?Supplier
    {
        $supplier = Supplier::find($id);
        if (! $supplier) {
            return null;
        }

        $contacts = $data['contacts'] ?? null;
        $bankAccounts = $data['bank_accounts'] ?? null;
        $documents = $data['documents'] ?? null;

        unset($data['contacts'], $data['bank_accounts'], $data['documents']);

        $supplier->update($data);

        if ($contacts !== null) {
            $supplier->contacts()->delete();
            foreach ($contacts as $contact) {
                $supplier->contacts()->create($contact);
            }
        }

        if ($bankAccounts !== null) {
            $supplier->bankAccounts()->delete();
            foreach ($bankAccounts as $bank) {
                $supplier->bankAccounts()->create($bank);
            }
        }

        if ($documents !== null) {
            $supplier->documents()->delete();
            foreach ($documents as $doc) {
                $supplier->documents()->create($doc);
            }
        }

        return $supplier;
    }

    public function delete(string $id): bool
    {
        $supplier = Supplier::find($id);

        return $supplier ? $supplier->delete() : false;
    }

    public function restore(string $id): bool
    {
        $supplier = Supplier::onlyTrashed()->find($id);

        return $supplier ? $supplier->restore() : false;
    }

    public function getMetrics(): array
    {
        return [
            'total_suppliers' => Supplier::count(),
            'pending_approvals' => Supplier::where('status', 'pending_approval')->count(),
            'total_outstanding' => Supplier::sum('outstanding_balance'),
            'average_rating' => Supplier::avg('rating') ?: 5.0,
        ];
    }
}
