<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\DeliveryAgent;
use App\Models\SalesOrder;
use App\Repositories\Contracts\DeliveryRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DeliveryService
{
    protected DeliveryRepositoryInterface $deliveryRepo;

    public function __construct(DeliveryRepositoryInterface $deliveryRepo)
    {
        $this->deliveryRepo = $deliveryRepo;
    }

    public function createAgent(array $data)
    {
        return $this->deliveryRepo->createAgent($data);
    }

    public function createVehicle(array $data)
    {
        return $this->deliveryRepo->createVehicle($data);
    }

    public function assignDelivery(array $data)
    {
        $data['status'] = 'assigned';
        $data['estimated_delivery_time'] = now()->addMinutes(45);
        $data['otp'] = strval(rand(1000, 9999));

        return $this->deliveryRepo->assignDelivery($data);
    }

    public function updateGPS(string $deliveryId, float $lat, float $lng): bool
    {
        return $this->deliveryRepo->updateGPS($deliveryId, $lat, $lng);
    }

    /**
     * OTP-verified checkout of sales order deliveries.
     */
    public function confirmDeliveryWithOTP(string $deliveryId, string $otp, string $signature, ?string $photoUrl): bool
    {
        return DB::transaction(function () use ($deliveryId, $otp, $signature, $photoUrl) {
            $delivery = Delivery::find($deliveryId);
            if (! $delivery || $delivery->status === 'delivered') {
                return false;
            }

            // 1. Verify OTP code
            if (! $this->deliveryRepo->verifyOTP($deliveryId, $otp)) {
                return false;
            }

            // 2. Upload POD (marks status = delivered)
            $this->deliveryRepo->uploadPOD($deliveryId, $signature, $photoUrl);

            // 3. Sync Sales Order parent state
            $order = SalesOrder::find($delivery->sales_order_id);
            if ($order) {
                $order->update([
                    'status' => 'delivered',
                    'shipping_status' => 'delivered',
                ]);
            }

            return true;
        });
    }

    /**
     * Compute delivery metrics.
     */
    public function getDeliveryAnalytics(): array
    {
        $assigned = Delivery::where('status', 'assigned')->count();
        $inTransit = Delivery::where('status', 'in_transit')->count();
        $completed = Delivery::where('status', 'delivered')->count();
        $activeAgents = DeliveryAgent::where('status', 'available')->count();

        return [
            'assigned_count' => $assigned,
            'transit_count' => $inTransit,
            'completed_count' => $completed,
            'active_agents' => $activeAgents,
        ];
    }
}
