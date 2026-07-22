<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BinInventory extends Model
{
    use HasUuids;

    protected $fillable = [
        'bin_id',
        'product_id',
        'quantity',
        'batch_number',
        'expiry_date',
    ];

    protected $casts = [
        'quantity' => 'float',
        'expiry_date' => 'date',
    ];

    public function bin(): BelongsTo
    {
        return $this->belongsTo(WarehouseBin::class, 'bin_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
