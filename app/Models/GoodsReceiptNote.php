<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GoodsReceiptNote extends Model
{
    use HasUuids;

    protected $fillable = [
        'code',
        'purchase_order_id',
        'received_date',
        'received_by',
        'status',
        'remarks',
    ];

    protected static function booted(): void
    {
        static::creating(function ($grn) {
            if (empty($grn->code)) {
                $grn->code = 'GRN-'.date('Y').'-'.strtoupper(Str::random(6));
            }
        });
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceiptNoteItem::class);
    }
}
