<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLedger extends Model
{
    use HasUuids;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'transaction_type',
        'quantity',
        'balance_after',
        'unit_price',
        'reference_type',
        'reference_id',
        'batch_number',
        'expiry_date',
    ];

    protected $casts = [
        'quantity' => 'float',
        'balance_after' => 'float',
        'unit_price' => 'float',
        'expiry_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
