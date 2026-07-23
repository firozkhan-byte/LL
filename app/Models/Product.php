<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'parent_id',
        'category_id',
        'brand_id',
        'manufacturer_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'qr_code_path',
        'hsn_code',
        'gst_rate',
        'liquor_type',
        'volume_ml',
        'alcohol_percentage',
        'mrp',
        'purchase_price',
        'selling_price',
        'origin_country',
        'origin_region',
        'expiry_tracking',
        'batch_tracking',
        'serial_tracking',
        'description',
        'status',
        'attributes',
        'tags',
        'hsn_code_id',
    ];

    protected $casts = [
        'attributes' => 'array',
        'tags' => 'array',
        'gst_rate' => 'float',
        'alcohol_percentage' => 'float',
        'mrp' => 'float',
        'purchase_price' => 'float',
        'selling_price' => 'float',
        'expiry_tracking' => 'boolean',
        'batch_tracking' => 'boolean',
        'serial_tracking' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name).'-'.strtolower(Str::random(4));
            }
            if (empty($product->sku)) {
                $product->sku = 'SKU-'.strtoupper(Str::random(8));
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->images()->where('is_primary', true)->first() ?? $this->images()->first();
    }

    public function hsnCode(): BelongsTo
    {
        return $this->belongsTo(HsnCode::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'sku', 'mrp', 'selling_price', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
