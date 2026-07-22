<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\POSService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class POSTerminal extends Component
{
    public array $cart = [];

    public string $searchProduct = '';

    public string $selectedWarehouseId = '';

    // Customer Lookup
    public string $customerPhone = '';

    public ?array $customerData = null;

    // Discount / Coupon Code
    public string $couponCode = '';

    public float $discountAmount = 0.00;

    public ?string $appliedCouponId = null;

    // Gift Card
    public string $giftCardNumber = '';

    public float $giftCardAppliedBalance = 0.00;

    public ?string $appliedGiftCardId = null;

    // Split Payments
    public bool $showingCheckoutModal = false;

    public float $cashPaid = 0.00;

    public float $upiPaid = 0.00;

    public float $cardPaid = 0.00;

    public float $giftCardPaid = 0.00;

    // Receipt Print
    public bool $showingReceiptModal = false;

    public ?string $printedSaleId = null;

    public ?array $receiptDetails = null;

    public function mount(): void
    {
        abort_if(Gate::denies('manage-company'), 403);

        $wh = Warehouse::first();
        if ($wh) {
            $this->selectedWarehouseId = $wh->id;
        }
    }

    public function render()
    {
        $products = Product::where('status', 'active');
        if (! empty($this->searchProduct)) {
            $products->where(function ($q) {
                $q->where('name', 'like', "%{$this->searchProduct}%")
                    ->orWhere('sku', 'like', "%{$this->searchProduct}%");
            });
        }

        $productsList = $products->take(12)->get();
        $warehouses = Warehouse::all();

        // Calculate Cart Summary
        $cartSubtotal = 0.00;
        foreach ($this->cart as $item) {
            $cartSubtotal += ($item['quantity'] * $item['price']);
        }

        // Apply discount limit rules
        $cartTotal = max(0.00, $cartSubtotal - $this->discountAmount - $this->giftCardAppliedBalance);
        $taxAmount = $cartTotal * 0.18; // default 18% GST inclusive logic simulation
        $cartFinal = $cartTotal + $taxAmount;

        return view('livewire.admin.pos-terminal', [
            'productsList' => $productsList,
            'warehouses' => $warehouses,
            'cartSubtotal' => $cartSubtotal,
            'cartFinal' => $cartFinal,
            'taxAmount' => $taxAmount,
        ])->layout('layouts.app');
    }

    public function addToCart(string $productId): void
    {
        $product = Product::find($productId);
        if (! $product) {
            return;
        }

        // Check if already in cart
        foreach ($this->cart as $index => $item) {
            if ($item['product_id'] === $productId) {
                $this->cart[$index]['quantity'] += 1;

                return;
            }
        }

        $price = floatval($product->selling_price);
        $batchNumber = null;

        $batch = \App\Models\ProductBatch::where('product_id', $productId)
            ->where('status', 'active')
            ->orderBy('expiry_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($batch) {
            $price = floatval($batch->selling_price);
            $batchNumber = $batch->batch_number;
        }

        $this->cart[] = [
            'product_id' => $product->id,
            'name' => $product->name . ($batchNumber ? " ({$batchNumber})" : ""),
            'quantity' => 1.00,
            'price' => $price,
            'batch_number' => $batchNumber,
        ];
    }

    public function updateQuantity(int $index, float $quantity): void
    {
        if ($quantity <= 0) {
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart);
        } else {
            $this->cart[$index]['quantity'] = $quantity;
        }
    }

    public function lookupCustomer(POSService $posService): void
    {
        $this->customerData = null;
        $customer = $posService->getCustomer($this->customerPhone);

        if ($customer) {
            $this->customerData = [
                'id' => $customer->id,
                'name' => $customer->name,
                'membership_type' => $customer->membership_type,
                'loyalty_points' => $customer->loyalty_points,
            ];
            session()->flash('cust_message', 'Loyalty Customer verified: '.$customer->name);
        } else {
            session()->flash('cust_error', 'No phone matches found in ERP membership records.');
        }
    }

    public function applyCoupon(POSService $posService): void
    {
        $this->discountAmount = 0.00;
        $coupon = $posService->getCoupon($this->couponCode);

        if ($coupon) {
            $subtotal = collect($this->cart)->sum(fn ($i) => $i['quantity'] * $i['price']);
            if ($subtotal >= $coupon->min_purchase_amount) {
                if ($coupon->discount_type === 'percentage') {
                    $this->discountAmount = $subtotal * ($coupon->discount_value / 100);
                } else {
                    $this->discountAmount = floatval($coupon->discount_value);
                }
                $this->appliedCouponId = $coupon->id;
                session()->flash('discount_message', 'Coupon Applied: -₹'.number_format($this->discountAmount, 2));
            } else {
                session()->flash('discount_error', 'Minimum purchase of ₹'.$coupon->min_purchase_amount.' required.');
            }
        } else {
            session()->flash('discount_error', 'Invalid or expired coupon code.');
        }
    }

    public function applyGiftCard(POSService $posService): void
    {
        $this->giftCardAppliedBalance = 0.00;
        $gc = $posService->getGiftCard($this->giftCardNumber);

        if ($gc) {
            $subtotal = collect($this->cart)->sum(fn ($i) => $i['quantity'] * $i['price']);
            $remainingTotal = max(0.00, $subtotal - $this->discountAmount);

            $this->giftCardAppliedBalance = min($remainingTotal, floatval($gc->balance));
            $this->appliedGiftCardId = $gc->id;
            $this->giftCardPaid = $this->giftCardAppliedBalance;

            session()->flash('gc_message', 'Gift Card applied balance: ₹'.number_format($this->giftCardAppliedBalance, 2));
        } else {
            session()->flash('gc_error', 'Invalid gift card number.');
        }
    }

    public function openCheckout(): void
    {
        if (empty($this->cart)) {
            return;
        }

        // Reset split payments to cart final total
        $sub = collect($this->cart)->sum(fn ($i) => $i['quantity'] * $i['price']);
        $total = ($sub - $this->discountAmount - $this->giftCardAppliedBalance) * 1.18; // Subtotal - Discounts + 18% GST

        $this->cashPaid = max(0.00, $total);
        $this->upiPaid = 0.00;
        $this->cardPaid = 0.00;
        $this->showingCheckoutModal = true;
    }

    public function saveCheckout(POSService $posService): void
    {
        $sub = collect($this->cart)->sum(fn ($i) => $i['quantity'] * $i['price']);
        $total = max(0.00, ($sub - $this->discountAmount - $this->giftCardAppliedBalance) * 1.18);

        $totalPaidSum = $this->cashPaid + $this->upiPaid + $this->cardPaid + $this->giftCardPaid;

        // Allow slight difference for rounding or require exact match
        if (abs($totalPaidSum - $total) > 0.1) {
            session()->flash('checkout_error', 'Payment sum (₹'.number_format($totalPaidSum, 2).') must equal checkout invoice total (₹'.number_format($total, 2).').');

            return;
        }

        $itemsPayload = [];
        foreach ($this->cart as $item) {
            $itemsPayload[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'discount_amount' => 0.00,
                'total_price' => $item['quantity'] * $item['price'],
            ];
        }

        $sale = $posService->checkout([
            'warehouse_id' => $this->selectedWarehouseId,
            'customer_id' => $this->customerData ? $this->customerData['id'] : null,
            'subtotal' => $sub,
            'discount_amount' => $this->discountAmount + $this->giftCardAppliedBalance,
            'tax_amount' => $total * 0.18,
            'total_amount' => $total,
            'payment_status' => 'paid',
            'payment_methods' => [
                'cash' => $this->cashPaid,
                'upi' => $this->upiPaid,
                'card' => $this->cardPaid,
                'gift_card' => $this->giftCardPaid,
            ],
            'created_by' => auth()->id() ?? User::first()->id,
            'items' => $itemsPayload,
        ]);

        // Load printed receipt details
        $this->printedSaleId = $sale->id;
        $this->receiptDetails = [
            'invoice_number' => $sale->invoice_number,
            'subtotal' => $sale->subtotal,
            'discount' => $sale->discount_amount,
            'total' => $sale->total_amount,
            'date' => $sale->created_at->format('Y-m-d H:i'),
            'customer_name' => $this->customerData ? $this->customerData['name'] : 'Walk-in Customer',
            'items' => $this->cart,
        ];

        // Clear cart
        $this->cart = [];
        $this->customerPhone = '';
        $this->customerData = null;
        $this->couponCode = '';
        $this->discountAmount = 0.00;
        $this->giftCardNumber = '';
        $this->giftCardAppliedBalance = 0.00;

        $this->showingCheckoutModal = false;
        $this->showingReceiptModal = true;
    }
}
