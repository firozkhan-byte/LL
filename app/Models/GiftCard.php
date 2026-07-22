<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    use HasUuids;

    protected $fillable = [
        'card_number',
        'balance',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'float',
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];
}
