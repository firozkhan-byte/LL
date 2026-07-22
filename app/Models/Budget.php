<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasUuids;

    protected $fillable = [
        'account_id',
        'fiscal_year',
        'allocated_amount',
        'spent_amount',
    ];

    protected $casts = [
        'allocated_amount' => 'float',
        'spent_amount' => 'float',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
