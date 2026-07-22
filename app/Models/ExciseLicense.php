<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExciseLicense extends Model
{
    use HasUuids;

    protected $fillable = [
        'license_number',
        'license_type',
        'state',
        'expiry_date',
        'status', // active, expired, pending_renewal
        'renewal_fee',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'renewal_fee' => 'float',
    ];

    public function permits(): HasMany
    {
        return $this->hasMany(ExcisePermit::class);
    }

    public function registers(): HasMany
    {
        return $this->hasMany(ExciseRegister::class);
    }
}
