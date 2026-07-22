<?php

namespace App\Services;

use App\Models\CrmCampaign;
use App\Models\CrmTicket;
use App\Models\CustomerProfile;
use App\Models\CustomerWallet;
use App\Repositories\Contracts\CRMRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CRMService
{
    protected CRMRepositoryInterface $crmRepo;

    public function __construct(CRMRepositoryInterface $crmRepo)
    {
        $this->crmRepo = $crmRepo;
    }

    public function adjustWallet(string $customerId, float $amount, string $type, ?string $refType = null, ?string $refId = null): CustomerWallet
    {
        return DB::transaction(function () use ($customerId, $amount, $type, $refType, $refId) {
            return $this->crmRepo->adjustWalletBalance($customerId, $amount, $type, $refType, $refId);
        });
    }

    public function createTicket(array $data): CrmTicket
    {
        return $this->crmRepo->createTicket($data);
    }

    public function resolveTicket(string $ticketId): bool
    {
        return $this->crmRepo->updateTicketStatus($ticketId, 'resolved');
    }

    public function getTickets(array $filters, int $perPage = 10)
    {
        return $this->crmRepo->getTickets($filters, $perPage);
    }

    public function dispatchCampaign(array $data): CrmCampaign
    {
        $data['status'] = 'sent';

        return $this->crmRepo->dispatchCampaign($data);
    }

    public function getCRMAnalytics(): array
    {
        $totalLiability = CustomerWallet::sum('balance');

        $openTickets = CrmTicket::where('status', 'open')->count();
        $inProgressTickets = CrmTicket::where('status', 'in_progress')->count();
        $resolvedTickets = CrmTicket::where('status', 'resolved')->count();

        // Calculate count of birthdays today
        $todayBirthdayCount = CustomerProfile::whereMonth('birthday', '=', now()->month)
            ->whereDay('birthday', '=', now()->day)
            ->count();

        return [
            'total_liability' => $totalLiability,
            'open_tickets' => $openTickets,
            'in_progress_tickets' => $inProgressTickets,
            'resolved_tickets' => $resolvedTickets,
            'today_birthday_count' => $todayBirthdayCount,
        ];
    }
}
