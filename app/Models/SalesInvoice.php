<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SalesInvoice extends Model
{
    use HasUuids;

    protected $fillable = [
        'invoice_number',
        'sales_order_id',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function ($inv) {
            if (empty($inv->invoice_number)) {
                $inv->invoice_number = 'INV-SL-'.date('Y').'-'.strtoupper(Str::random(6));
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }
}
