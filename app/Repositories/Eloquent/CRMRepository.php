<?php

namespace App\Repositories\Eloquent;

use App\Models\CrmCampaign;
use App\Models\CrmTicket;
use App\Models\CustomerWallet;
use App\Models\CustomerWalletTransaction;
use App\Repositories\Contracts\CRMRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CRMRepository implements CRMRepositoryInterface
{
    public function adjustWalletBalance(string $customerId, float $amount, string $type, ?string $refType = null, ?string $refId = null): CustomerWallet
    {
        $wallet = CustomerWallet::firstOrCreate(
            ['customer_id' => $customerId],
            ['balance' => 0.00, 'currency' => 'INR']
        );

        if ($type === 'deposit' || $type === 'refund') {
            $wallet->balance += $amount;
        } else {
            $wallet->balance -= $amount;
        }

        $wallet->save();

        CustomerWalletTransaction::create([
            'customer_wallet_id' => $wallet->id,
            'transaction_type' => $type,
            'amount' => $amount,
            'reference_type' => $refType,
            'reference_id' => $refId,
        ]);

        return $wallet;
    }

    public function getWalletBalance(string $customerId): float
    {
        $wallet = CustomerWallet::where('customer_id', $customerId)->first();

        return $wallet ? $wallet->balance : 0.00;
    }

    public function createTicket(array $data): CrmTicket
    {
        return CrmTicket::create($data);
    }

    public function updateTicketStatus(string $ticketId, string $status): bool
    {
        $ticket = CrmTicket::find($ticketId);
        if (! $ticket) {
            return false;
        }

        return $ticket->update(['status' => $status]);
    }

    public function getTickets(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = CrmTicket::with(['customer', 'assignee']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (! empty($filters['search'])) {
            $query->where('subject', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function dispatchCampaign(array $data): CrmCampaign
    {
        return CrmCampaign::create($data);
    }
}
