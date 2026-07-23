<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PurchaseOrder extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'code',
        'supplier_id',
        'purchase_requisition_id',
        'po_date',
        'payment_terms',
        'status',
        'subtotal',
        'tax_amount',
        'total_amount',
        'approved_by',
        'remarks',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'tax_amount' => 'float',
        'total_amount' => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function ($po) {
            if (empty($po->code)) {
                $po->code = 'PO-'.date('Y').'-'.strtoupper(Str::random(6));
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class, 'purchase_requisition_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function grns(): HasMany
    {
        return $this->hasMany(GoodsReceiptNote::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['code', 'status', 'total_amount'])
            ->logOnlyDirty();
    }
}
