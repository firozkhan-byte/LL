<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class StockAdjustment extends Model
{
    use HasUuids;

    protected $fillable = [
        'code',
        'warehouse_id',
        'reason',
        'status',
        'created_by',
        'approved_by',
        'remarks',
    ];

    protected static function booted(): void
    {
        static::creating(function ($adj) {
            if (empty($adj->code)) {
                $adj->code = 'ADJ-'.date('Y').'-'.strtoupper(Str::random(6));
            }
        });
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }
}
