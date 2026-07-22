<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'membership_type',
        'loyalty_points',
    ];

    public function posSales(): HasMany
    {
        return $this->hasMany(PosSale::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(CustomerWallet::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(CrmTicket::class);
    }

    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }
}
