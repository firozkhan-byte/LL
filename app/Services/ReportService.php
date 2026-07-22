<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\BinInventory;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\ExciseLicense;
use App\Models\JournalLine;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Compile enterprise reporting metrics.
     */
    public function generateEnterpriseReport(string $startDate, string $endDate): array
    {
        // 1. Sales
        $salesCount = SalesOrder::whereBetween('created_at', [$startDate, $endDate])->count();
        $salesTotal = SalesOrder::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $averageOrderValue = $salesCount > 0 ? ($salesTotal / $salesCount) : 0.00;

        // 2. Purchase
        $purchaseCount = PurchaseOrder::whereBetween('created_at', [$startDate, $endDate])->count();
        $purchaseTotal = PurchaseOrder::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');

        // 3. Inventory
        $inventoryItems = BinInventory::sum('quantity') ?? 0;
        $inventoryValuation = BinInventory::join('products', 'bin_inventories.product_id', '=', 'products.id')
            ->sum(DB::raw('bin_inventories.quantity * products.selling_price')) ?? 0.00;
        $activeProductsCount = Product::count();

        // 4. Finance
        $totalDebits = JournalLine::whereBetween('created_at', [$startDate, $endDate])->sum('debit');
        $totalCredits = JournalLine::whereBetween('created_at', [$startDate, $endDate])->sum('credit');

        // 5. GST & Excise
        $outputGst = SalesOrder::whereBetween('created_at', [$startDate, $endDate])->sum('tax_amount');
        $inputGst = PurchaseOrder::whereBetween('created_at', [$startDate, $endDate])->sum('tax_amount');
        $netGst = max(0, $outputGst - $inputGst);
        $activeLicenses = ExciseLicense::where('status', 'active')->count();

        // 6. Customers
        $totalCustomers = Customer::count();
        $newCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])->count();

        // 7. Suppliers
        $totalSuppliers = Supplier::count();

        // 8. Warehouses
        $totalWarehouses = Warehouse::count();

        // 9. HR
        $totalEmployees = Employee::where('status', 'active')->count();
        $attendanceCount = Attendance::whereBetween('date', [$startDate, $endDate])->count();
        $presentCount = Attendance::whereBetween('date', [$startDate, $endDate])
            ->whereIn('status', ['present', 'late'])
            ->count();
        $attendanceRate = $attendanceCount > 0
            ? round(($presentCount / $attendanceCount) * 100, 1)
            : 100.0;

        // 10. Branches
        $totalBranches = Branch::count();

        return [
            'sales' => [
                'count' => $salesCount,
                'total' => $salesTotal,
                'aov' => $averageOrderValue,
            ],
            'purchase' => [
                'count' => $purchaseCount,
                'total' => $purchaseTotal,
            ],
            'inventory' => [
                'total_items' => $inventoryItems,
                'valuation' => $inventoryValuation,
                'products_count' => $activeProductsCount,
            ],
            'finance' => [
                'debits' => $totalDebits,
                'credits' => $totalCredits,
            ],
            'compliance' => [
                'output_gst' => $outputGst,
                'input_gst' => $inputGst,
                'net_gst' => $netGst,
                'active_licenses' => $activeLicenses,
            ],
            'customers' => [
                'total' => $totalCustomers,
                'new' => $newCustomers,
            ],
            'suppliers' => [
                'total' => $totalSuppliers,
            ],
            'warehouses' => [
                'total' => $totalWarehouses,
            ],
            'hr' => [
                'headcount' => $totalEmployees,
                'attendance_rate' => $attendanceRate,
            ],
            'branches' => [
                'total' => $totalBranches,
            ],
        ];
    }
}
