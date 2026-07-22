<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SalesReturn extends Model
{
    use HasUuids;

    protected $fillable = [
        'return_number',
        'sales_order_id',
        'reason',
        'refund_amount',
        'status',
    ];

    protected $casts = [
        'refund_amount' => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function ($ret) {
            if (empty($ret->return_number)) {
                $ret->return_number = 'RET-'.date('Y').'-'.strtoupper(Str::random(6));
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesReturnItem::class);
    }
}
