<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesReturnItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'sales_return_id',
        'product_id',
        'quantity',
        'refund_unit_price',
    ];

    protected $casts = [
        'quantity' => 'float',
        'refund_unit_price' => 'float',
    ];

    public function salesReturn(): BelongsTo
    {
        return $this->belongsTo(SalesReturn::class, 'sales_return_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
