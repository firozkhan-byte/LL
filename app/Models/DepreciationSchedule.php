<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DepreciationSchedule extends Model
{
    use HasUuids;

    protected $fillable = [
        'asset_name',
        'purchase_cost',
        'salvage_value',
        'useful_life_years',
        'depreciation_method', // straight_line
        'current_value',
    ];

    protected $casts = [
        'purchase_cost' => 'float',
        'salvage_value' => 'float',
        'current_value' => 'float',
    ];
}
