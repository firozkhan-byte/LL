<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerProfile extends Model
{
    use HasUuids;

    protected $fillable = [
        'customer_id',
        'birthday',
        'anniversary',
        'preferences',
        'notes',
    ];

    protected $casts = [
        'preferences' => 'array',
        'birthday' => 'date',
        'anniversary' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
