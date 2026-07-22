<?php

namespace App\Repositories\Contracts;

use App\Models\Delivery;
use App\Models\DeliveryAgent;
use App\Models\Vehicle;
use Illuminate\Pagination\LengthAwarePaginator;

interface DeliveryRepositoryInterface
{
    public function createAgent(array $data): DeliveryAgent;

    public function createVehicle(array $data): Vehicle;

    public function assignDelivery(array $data): Delivery;

    public function updateDeliveryStatus(string $deliveryId, string $status): bool;

    public function updateGPS(string $deliveryId, float $lat, float $lng): bool;

    public function verifyOTP(string $deliveryId, string $otp): bool;

    public function uploadPOD(string $deliveryId, string $signature, ?string $photoUrl): bool;

    public function getDeliveries(array $filters, int $perPage = 10): LengthAwarePaginator;
}
