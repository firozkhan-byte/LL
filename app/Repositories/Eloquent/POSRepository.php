<?php

namespace App\Repositories\Eloquent;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\GiftCard;
use App\Models\PosSale;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use App\Repositories\Contracts\POSRepositoryInterface;

class POSRepository implements POSRepositoryInterface
{
    public function findCustomerByPhone(string $phone): ?Customer
    {
        return Customer::where('phone', $phone)->first();
    }

    public function findCouponByCode(string $code): ?Coupon
    {
        return Coupon::where('code', $code)
            ->where('is_active', true)
            ->first();
    }

    public function findGiftCardByNumber(string $cardNumber): ?GiftCard
    {
        return GiftCard::where('card_number', $cardNumber)
            ->where('is_active', true)
            ->first();
    }

    public function createPOSSale(array $data): PosSale
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $sale = PosSale::create($data);
        foreach ($items as $item) {
            $sale->items()->create($item);

            // Integrate with Central Stock Ledger to decrement quantities
            $inventoryRepo = app(InventoryRepositoryInterface::class);
            if ($inventoryRepo) {
                $inventoryRepo->recordLedgerEntry([
                    'product_id' => $item['product_id'],
                    'warehouse_id' => $data['warehouse_id'],
                    'transaction_type' => 'sale',
                    'quantity' => -floatval($item['quantity']),
                    'reference_type' => 'PosSale',
                    'reference_id' => $sale->id,
                    'unit_price' => $item['unit_price'],
                ]);
            }
        }

        // Award loyalty points to customer if provided
        if (! empty($data['customer_id'])) {
            $customer = Customer::find($data['customer_id']);
            if ($customer) {
                // Award 1 loyalty point per ₹100 spent
                $pointsEarned = floor(floatval($data['total_amount']) / 100);
                $customer->increment('loyalty_points', $pointsEarned);
            }
        }

        return $sale;
    }
}
