<?php

namespace App\Repositories\Contracts;

use App\Models\SalesOrder;
use App\Models\SalesReturn;
use Illuminate\Pagination\LengthAwarePaginator;

interface SalesRepositoryInterface
{
    public function getSalesOrders(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function getSalesInvoices(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function getSalesReturns(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function createSalesOrder(array $data): SalesOrder;

    public function transitionOrderStatus(string $orderId, string $status): bool;

    public function processSalesReturn(array $data): SalesReturn;
}
