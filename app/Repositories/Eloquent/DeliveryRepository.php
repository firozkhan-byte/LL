<?php

namespace App\Repositories\Eloquent;

use App\Models\Delivery;
use App\Models\DeliveryAgent;
use App\Models\Vehicle;
use App\Repositories\Contracts\DeliveryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class DeliveryRepository implements DeliveryRepositoryInterface
{
    public function createAgent(array $data): DeliveryAgent
    {
        return DeliveryAgent::create($data);
    }

    public function createVehicle(array $data): Vehicle
    {
        return Vehicle::create($data);
    }

    public function assignDelivery(array $data): Delivery
    {
        return Delivery::create($data);
    }

    public function updateDeliveryStatus(string $deliveryId, string $status): bool
    {
        $delivery = Delivery::find($deliveryId);
        if (! $delivery) {
            return false;
        }

        return $delivery->update(['status' => $status]);
    }

    public function updateGPS(string $deliveryId, float $lat, float $lng): bool
    {
        $delivery = Delivery::find($deliveryId);
        if (! $delivery) {
            return false;
        }

        return $delivery->update([
            'gps_lat' => $lat,
            'gps_lng' => $lng,
        ]);
    }

    public function verifyOTP(string $deliveryId, string $otp): bool
    {
        $delivery = Delivery::find($deliveryId);
        if (! $delivery) {
            return false;
        }

        return $delivery->otp === $otp;
    }

    public function uploadPOD(string $deliveryId, string $signature, ?string $photoUrl): bool
    {
        $delivery = Delivery::find($deliveryId);
        if (! $delivery) {
            return false;
        }

        return $delivery->update([
            'proof_signature' => $signature,
            'proof_photo_url' => $photoUrl,
            'status' => 'delivered',
            'actual_delivery_time' => now(),
        ]);
    }

    public function getDeliveries(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Delivery::with(['salesOrder', 'agent', 'vehicle']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['agent_id'])) {
            $query->where('delivery_agent_id', $filters['agent_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
