<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExciseRegister extends Model
{
    use HasUuids;

    protected $fillable = [
        'transaction_date',
        'excise_license_id',
        'product_id',
        'opening_balance',
        'received_quantity',
        'sold_quantity',
        'closing_balance',
        'excise_duty_paid',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'opening_balance' => 'float',
        'received_quantity' => 'float',
        'sold_quantity' => 'float',
        'closing_balance' => 'float',
        'excise_duty_paid' => 'float',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(ExciseLicense::class, 'excise_license_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
