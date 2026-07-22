<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerWalletTransaction extends Model
{
    use HasUuids;

    protected $fillable = [
        'customer_wallet_id',
        'transaction_type', // deposit, withdrawal, refund, purchase
        'amount',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(CustomerWallet::class, 'customer_wallet_id');
    }
}
