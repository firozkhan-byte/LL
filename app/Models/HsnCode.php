<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HsnCode extends Model
{
    use HasUuids;

    protected $fillable = [
        'code',
        'description',
        'gst_rate',
        'excise_duty_rate',
    ];

    protected $casts = [
        'gst_rate' => 'float',
        'excise_duty_rate' => 'float',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
