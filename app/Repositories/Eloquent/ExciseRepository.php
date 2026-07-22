<?php

namespace App\Repositories\Eloquent;

use App\Models\ExciseLicense;
use App\Models\ExcisePermit;
use App\Models\ExciseRegister;
use App\Models\HsnCode;
use App\Repositories\Contracts\ExciseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ExciseRepository implements ExciseRepositoryInterface
{
    public function renewLicense(string $licenseId, string $newExpiryDate): bool
    {
        $license = ExciseLicense::find($licenseId);
        if (! $license) {
            return false;
        }

        return $license->update([
            'expiry_date' => $newExpiryDate,
            'status' => 'active',
        ]);
    }

    public function createPermit(array $data): ExcisePermit
    {
        return ExcisePermit::create($data);
    }

    public function updatePermitStatus(string $permitId, string $status): bool
    {
        $permit = ExcisePermit::find($permitId);
        if (! $permit) {
            return false;
        }

        return $permit->update(['status' => $status]);
    }

    public function getPermits(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = ExcisePermit::with(['license', 'supplier']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['search'])) {
            $query->where('permit_number', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function addRegisterEntry(array $data): ExciseRegister
    {
        return ExciseRegister::create($data);
    }

    public function getExciseRegister(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = ExciseRegister::with(['license', 'product']);

        if (! empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }
        if (! empty($filters['date'])) {
            $query->whereDate('transaction_date', $filters['date']);
        }

        return $query->orderBy('transaction_date', 'desc')->paginate($perPage);
    }

    public function createHsnCode(array $data): HsnCode
    {
        return HsnCode::create($data);
    }

    public function getHsnCodes(): array
    {
        return HsnCode::orderBy('code')->get()->all();
    }
}
