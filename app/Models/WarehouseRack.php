<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseRack extends Model
{
    use HasUuids;

    protected $fillable = [
        'warehouse_id',
        'code',
        'name',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function shelves(): HasMany
    {
        return $this->hasMany(WarehouseShelf::class, 'rack_id');
    }
}
