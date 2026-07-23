<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ProductBatch extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'product_id',
        'batch_number',
        'expiry_date',
        'mrp',
        'purchase_price',
        'selling_price',
        'is_markdown',
        'markdown_percent',
        'status',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'mrp' => 'float',
        'purchase_price' => 'float',
        'selling_price' => 'float',
        'is_markdown' => 'boolean',
        'markdown_percent' => 'float',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['batch_number', 'expiry_date', 'selling_price', 'is_markdown', 'markdown_percent', 'status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
