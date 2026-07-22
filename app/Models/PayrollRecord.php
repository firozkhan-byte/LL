<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollRecord extends Model
{
    use HasUuids;

    protected $fillable = [
        'employee_id',
        'payment_date',
        'month',
        'year',
        'basic_salary',
        'allowances',
        'deductions',
        'net_salary',
        'status', // unpaid, paid
    ];

    protected $casts = [
        'payment_date' => 'date',
        'basic_salary' => 'float',
        'allowances' => 'float',
        'deductions' => 'float',
        'net_salary' => 'float',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
