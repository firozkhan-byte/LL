<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Supplier extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'gstin',
        'pan',
        'payment_terms_days',
        'credit_limit',
        'rating',
        'outstanding_balance',
        'status',
    ];

    protected $casts = [
        'credit_limit' => 'float',
        'rating' => 'float',
        'outstanding_balance' => 'float',
        'payment_terms_days' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($supplier) {
            if (empty($supplier->code)) {
                $supplier->code = 'SUP-'.date('Y').'-'.strtoupper(Str::random(6));
            }
        });
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(SupplierContact::class);
    }

    public function primaryContact()
    {
        return $this->contacts()->where('is_primary', true)->first() ?? $this->contacts()->first();
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(SupplierBankAccount::class);
    }

    public function primaryBankAccount()
    {
        return $this->bankAccounts()->where('is_primary', true)->first() ?? $this->bankAccounts()->first();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(SupplierDocument::class);
    }

    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'status', 'outstanding_balance'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
