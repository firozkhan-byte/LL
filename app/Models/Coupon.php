<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasUuids;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_purchase_amount',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'float',
        'min_purchase_amount' => 'float',
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];
}
