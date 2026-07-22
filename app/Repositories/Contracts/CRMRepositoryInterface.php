<?php

namespace App\Repositories\Contracts;

use App\Models\CrmCampaign;
use App\Models\CrmTicket;
use App\Models\CustomerWallet;
use Illuminate\Pagination\LengthAwarePaginator;

interface CRMRepositoryInterface
{
    public function adjustWalletBalance(string $customerId, float $amount, string $type, ?string $refType = null, ?string $refId = null): CustomerWallet;

    public function getWalletBalance(string $customerId): float;

    public function createTicket(array $data): CrmTicket;

    public function updateTicketStatus(string $ticketId, string $status): bool;

    public function getTickets(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function dispatchCampaign(array $data): CrmCampaign;
}
