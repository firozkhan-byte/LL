<?php

namespace App\Repositories\Contracts;

use App\Models\ExcisePermit;
use App\Models\ExciseRegister;
use App\Models\HsnCode;
use Illuminate\Pagination\LengthAwarePaginator;

interface ExciseRepositoryInterface
{
    public function renewLicense(string $licenseId, string $newExpiryDate): bool;

    public function createPermit(array $data): ExcisePermit;

    public function updatePermitStatus(string $permitId, string $status): bool;

    public function getPermits(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function addRegisterEntry(array $data): ExciseRegister;

    public function getExciseRegister(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function createHsnCode(array $data): HsnCode;

    public function getHsnCodes(): array;
}
