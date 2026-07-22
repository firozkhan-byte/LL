<?php

namespace App\Repositories\Contracts;

use App\Models\Approval;
use Illuminate\Support\Collection;

interface ApprovalRepositoryInterface
{
    public function allPending(): Collection;

    public function find(string $id): ?Approval;

    public function create(array $data): Approval;

    public function updateStatus(string $id, string $status, ?string $approverId, ?string $rejectionReason = null): ?Approval;
}
