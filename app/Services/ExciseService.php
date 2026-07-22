<?php

namespace App\Services;

use App\Models\ExciseRegister;
use App\Models\PosSaleItem;
use App\Models\Product;
use App\Models\PurchaseOrderItem;
use App\Models\SalesOrderItem;
use App\Repositories\Contracts\ExciseRepositoryInterface;

class ExciseService
{
    protected ExciseRepositoryInterface $exciseRepo;

    public function __construct(ExciseRepositoryInterface $exciseRepo)
    {
        $this->exciseRepo = $exciseRepo;
    }

    public function renewLicense(string $licenseId, string $newExpiryDate): bool
    {
        return $this->exciseRepo->renewLicense($licenseId, $newExpiryDate);
    }

    public function utilizePermit(string $permitId): bool
    {
        return $this->exciseRepo->updatePermitStatus($permitId, 'utilized');
    }

    /**
     * Compute GSTR1 (Sales outward supplies) and GSTR3B tax summaries.
     */
    public function calculateGSTSummary(string $startDate, string $endDate): array
    {
        // 1. GSTR1 Outward supplies (collected CGST, SGST, IGST from completed sales)
        $posSalesQuery = PosSaleItem::with(['product.hsnCode', 'sale'])
            ->whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            })->get();

        $salesOrdersQuery = SalesOrderItem::with(['product.hsnCode', 'order'])
            ->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
                    ->where('status', 'delivered');
            })->get();

        $outputTaxDetails = [];
        $totalTaxableSales = 0.00;
        $totalCGSTCollected = 0.00;
        $totalSGSTCollected = 0.00;
        $totalIGSTCollected = 0.00;

        // Process POS Sales items
        foreach ($posSalesQuery as $item) {
            $gstRate = $item->product->hsnCode->gst_rate ?? 18.00;
            $taxableValue = $item->subtotal / (1 + ($gstRate / 100));
            $taxCollected = $item->subtotal - $taxableValue;

            $totalTaxableSales += $taxableValue;
            $totalCGSTCollected += ($taxCollected / 2);
            $totalSGSTCollected += ($taxCollected / 2);

            $hsn = $item->product->hsnCode->code ?? '2208';
            if (! isset($outputTaxDetails[$hsn])) {
                $outputTaxDetails[$hsn] = ['taxable' => 0.00, 'cgst' => 0.00, 'sgst' => 0.00, 'igst' => 0.00];
            }
            $outputTaxDetails[$hsn]['taxable'] += $taxableValue;
            $outputTaxDetails[$hsn]['cgst'] += ($taxCollected / 2);
            $outputTaxDetails[$hsn]['sgst'] += ($taxCollected / 2);
        }

        // Process Sales orders items
        foreach ($salesOrdersQuery as $item) {
            $gstRate = $item->product->hsnCode->gst_rate ?? 18.00;
            $taxableValue = $item->subtotal / (1 + ($gstRate / 100));
            $taxCollected = $item->subtotal - $taxableValue;

            $totalTaxableSales += $taxableValue;
            // Let's assume intra-state CGST/SGST for simplicity
            $totalCGSTCollected += ($taxCollected / 2);
            $totalSGSTCollected += ($taxCollected / 2);

            $hsn = $item->product->hsnCode->code ?? '2208';
            if (! isset($outputTaxDetails[$hsn])) {
                $outputTaxDetails[$hsn] = ['taxable' => 0.00, 'cgst' => 0.00, 'sgst' => 0.00, 'igst' => 0.00];
            }
            $outputTaxDetails[$hsn]['taxable'] += $taxableValue;
            $outputTaxDetails[$hsn]['cgst'] += ($taxCollected / 2);
            $outputTaxDetails[$hsn]['sgst'] += ($taxCollected / 2);
        }

        // 2. GSTR3B Input Tax Credit (ITC from purchase records)
        $purchasesQuery = PurchaseOrderItem::with('product.hsnCode')
            ->whereHas('purchaseOrder', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
                    ->where('status', 'received');
            })->get();

        $totalInputTaxCredit = 0.00;
        foreach ($purchasesQuery as $item) {
            $gstRate = $item->product->hsnCode->gst_rate ?? 18.00;
            $taxableCost = $item->total_amount / (1 + ($gstRate / 100));
            $totalInputTaxCredit += ($item->total_amount - $taxableCost);
        }

        $totalOutputTax = $totalCGSTCollected + $totalSGSTCollected + $totalIGSTCollected;
        $netTaxPayable = max(0.00, $totalOutputTax - $totalInputTaxCredit);

        return [
            'gstr1_outward_supplies' => $outputTaxDetails,
            'total_taxable_sales' => $totalTaxableSales,
            'total_cgst' => $totalCGSTCollected,
            'total_sgst' => $totalSGSTCollected,
            'total_igst' => $totalIGSTCollected,
            'total_output_tax' => $totalOutputTax,
            'total_input_credit' => $totalInputTaxCredit,
            'net_gst_payable' => $netTaxPayable,
        ];
    }

    /**
     * Compute and save a daily excise register record for a product.
     */
    public function generateDailyExciseRegister(string $date, string $licenseId, string $productId): ExciseRegister
    {
        // 1. Calculate opening balance: closing balance of the day before
        $yesterday = date('Y-m-d', strtotime($date.' -1 day'));
        $prevRegister = ExciseRegister::where('product_id', $productId)
            ->where('excise_license_id', $licenseId)
            ->whereDate('transaction_date', $yesterday)
            ->first();

        $openingBalance = $prevRegister ? $prevRegister->closing_balance : 100.00; // default seed fallback

        // 2. Calculate received quantity: from purchase receipts
        $received = PurchaseOrderItem::where('product_id', $productId)
            ->whereHas('purchaseOrder', function ($q) use ($date) {
                $q->whereDate('created_at', $date)
                    ->where('status', 'received');
            })->sum('quantity');

        // 3. Calculate sold quantity: from POS sales & corporate orders
        $posSold = PosSaleItem::where('product_id', $productId)
            ->whereHas('sale', function ($q) use ($date) {
                $q->whereDate('created_at', $date);
            })->sum('quantity');

        $corpSold = SalesOrderItem::where('product_id', $productId)
            ->whereHas('order', function ($q) use ($date) {
                $q->whereDate('created_at', $date)
                    ->where('status', 'delivered');
            })->sum('quantity');

        $totalSold = $posSold + $corpSold;

        $closingBalance = $openingBalance + $received - $totalSold;

        // Fetch product's excise duty rate
        $product = Product::with('hsnCode')->find($productId);
        $dutyRate = $product->hsnCode->excise_duty_rate ?? 0.00;
        $dutyPaid = $received * $dutyRate;

        // Upsert register row
        return ExciseRegister::updateOrCreate(
            ['transaction_date' => $date, 'excise_license_id' => $licenseId, 'product_id' => $productId],
            [
                'opening_balance' => $openingBalance,
                'received_quantity' => $received,
                'sold_quantity' => $totalSold,
                'closing_balance' => $closingBalance,
                'excise_duty_paid' => $dutyPaid,
            ]
        );
    }
}
