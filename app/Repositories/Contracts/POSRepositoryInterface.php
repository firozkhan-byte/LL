<?php

namespace App\Repositories\Contracts;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\GiftCard;
use App\Models\PosSale;

interface POSRepositoryInterface
{
    public function findCustomerByPhone(string $phone): ?Customer;

    public function findCouponByCode(string $code): ?Coupon;

    public function findGiftCardByNumber(string $cardNumber): ?GiftCard;

    public function createPOSSale(array $data): PosSale;
}
