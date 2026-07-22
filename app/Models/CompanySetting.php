<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySetting extends Model
{
    use HasUuids;

    protected $fillable = [
        'company_id',
        'currency',
        'timezone',
        'fiscal_year_start',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
