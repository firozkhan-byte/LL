<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\GiftCard;
use App\Models\PosSale;
use App\Repositories\Contracts\POSRepositoryInterface;

class POSService
{
    protected POSRepositoryInterface $posRepository;

    public function __construct(POSRepositoryInterface $posRepository)
    {
        $this->posRepository = $posRepository;
    }

    public function getCustomer(string $phone): ?Customer
    {
        return $this->posRepository->findCustomerByPhone($phone);
    }

    public function getCoupon(string $code): ?Coupon
    {
        return $this->posRepository->findCouponByCode($code);
    }

    public function getGiftCard(string $cardNumber): ?GiftCard
    {
        return $this->posRepository->findGiftCardByNumber($cardNumber);
    }

    public function checkout(array $data): PosSale
    {
        return $this->posRepository->createPOSSale($data);
    }
}
