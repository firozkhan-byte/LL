<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PosSale extends Model
{
    use HasUuids;

    protected $fillable = [
        'invoice_number',
        'warehouse_id',
        'customer_id',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'payment_status',
        'payment_methods',
        'created_by',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'discount_amount' => 'float',
        'tax_amount' => 'float',
        'total_amount' => 'float',
        'payment_methods' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($sale) {
            if (empty($sale->invoice_number)) {
                $sale->invoice_number = 'INV-POS-'.date('Y').'-'.strtoupper(Str::random(6));
            }
        });
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PosSaleItem::class);
    }
}
