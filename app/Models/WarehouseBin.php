<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseBin extends Model
{
    use HasUuids;

    protected $fillable = [
        'shelf_id',
        'code',
        'name',
        'capacity_weight',
    ];

    protected $casts = [
        'capacity_weight' => 'float',
    ];

    public function shelf(): BelongsTo
    {
        return $this->belongsTo(WarehouseShelf::class, 'shelf_id');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(BinInventory::class, 'bin_id');
    }
}
