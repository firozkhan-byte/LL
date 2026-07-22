<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class SalesOrder extends Model
{
    use HasUuids;

    protected $fillable = [
        'order_number',
        'customer_id',
        'warehouse_id',
        'order_type',
        'status',
        'payment_status',
        'delivery_address',
        'subtotal',
        'tax_amount',
        'total_amount',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'tax_amount' => 'float',
        'total_amount' => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'SO-'.date('Y').'-'.strtoupper(Str::random(6));
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(SalesInvoice::class);
    }
}
