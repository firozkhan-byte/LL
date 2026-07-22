<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PurchaseRequisition extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'code',
        'requested_by',
        'needed_by_date',
        'status',
        'remarks',
    ];

    protected static function booted(): void
    {
        static::creating(function ($requisition) {
            if (empty($requisition->code)) {
                $requisition->code = 'REQ-'.date('Y').'-'.strtoupper(Str::random(6));
            }
        });
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionItem::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['code', 'status'])
            ->logOnlyDirty();
    }
}
