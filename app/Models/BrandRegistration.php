<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class BrandRegistration extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'brand_id',
        'state',
        'excise_code',
        'expiry_date',
        'status',
        'registration_fee',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'registration_fee' => 'float',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['state', 'excise_code', 'expiry_date', 'status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
