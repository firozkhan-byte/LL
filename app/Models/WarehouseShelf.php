<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseShelf extends Model
{
    use HasUuids;

    protected $fillable = [
        'rack_id',
        'code',
        'name',
    ];

    public function rack(): BelongsTo
    {
        return $this->belongsTo(WarehouseRack::class, 'rack_id');
    }

    public function bins(): HasMany
    {
        return $this->hasMany(WarehouseBin::class, 'shelf_id');
    }
}
