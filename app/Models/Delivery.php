<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasUuids;

    protected $fillable = [
        'sales_order_id',
        'delivery_agent_id',
        'vehicle_id',
        'status', // assigned, in_transit, delivered, failed
        'otp',
        'gps_lat',
        'gps_lng',
        'proof_signature',
        'proof_photo_url',
        'estimated_delivery_time',
        'actual_delivery_time',
    ];

    protected $casts = [
        'gps_lat' => 'float',
        'gps_lng' => 'float',
        'estimated_delivery_time' => 'datetime',
        'actual_delivery_time' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($deliv) {
            if (empty($deliv->otp)) {
                $deliv->otp = strval(rand(1000, 9999));
            }
        });
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(DeliveryAgent::class, 'delivery_agent_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
