<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Company extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'name',
        'registration_number',
        'tax_number',
        'email',
        'phone',
        'website',
        'status',
        'logo_path',
    ];

    public function settings(): HasOne
    {
        return $this->hasOne(CompanySetting::class);
    }

    public function regionalOffices(): HasMany
    {
        return $this->hasMany(RegionalOffice::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function businessUnits(): HasMany
    {
        return $this->hasMany(BusinessUnit::class);
    }

    public function costCenters(): HasMany
    {
        return $this->hasMany(CostCenter::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'status', 'email', 'phone'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
