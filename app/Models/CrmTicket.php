<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmTicket extends Model
{
    use HasUuids;

    protected $fillable = [
        'customer_id',
        'type', // feedback, complaint, support
        'subject',
        'description',
        'status', // open, in_progress, resolved
        'priority', // low, medium, high
        'assigned_to',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
