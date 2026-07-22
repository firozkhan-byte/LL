<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CreditNote extends Model
{
    use HasUuids;

    protected $fillable = [
        'note_number',
        'customer_id',
        'sales_return_id',
        'amount',
        'status',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function ($cn) {
            if (empty($cn->note_number)) {
                $cn->note_number = 'CN-'.date('Y').'-'.strtoupper(Str::random(6));
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesReturn(): BelongsTo
    {
        return $this->belongsTo(SalesReturn::class);
    }
}
