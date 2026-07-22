<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class StockTransfer extends Model
{
    use HasUuids;

    protected $fillable = [
        'code',
        'from_warehouse_id',
        'to_warehouse_id',
        'status',
        'remarks',
    ];

    protected static function booted(): void
    {
        static::creating(function ($transfer) {
            if (empty($transfer->code)) {
                $transfer->code = 'TR-'.date('Y').'-'.strtoupper(Str::random(6));
            }
        });
    }

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockTransferItem::class);
    }
}
