<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExcisePermit extends Model
{
    use HasUuids;

    protected $fillable = [
        'permit_number',
        'excise_license_id',
        'supplier_id',
        'issue_date',
        'expiry_date',
        'status', // pending, utilized, expired
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(ExciseLicense::class, 'excise_license_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
