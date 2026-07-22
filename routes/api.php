<?php

use App\Http\Controllers\Api\v1\AIApiController;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\CompanyApiController;
use App\Http\Controllers\Api\v1\CRMApiController;
use App\Http\Controllers\Api\v1\DeliveryApiController;
use App\Http\Controllers\Api\v1\ExciseApiController;
use App\Http\Controllers\Api\v1\FinanceApiController;
use App\Http\Controllers\Api\v1\HRMApiController;
use App\Http\Controllers\Api\v1\InventoryApiController;
use App\Http\Controllers\Api\v1\POSApiController;
use App\Http\Controllers\Api\v1\ProductApiController;
use App\Http\Controllers\Api\v1\PurchaseApiController;
use App\Http\Controllers\Api\v1\ReportApiController;
use App\Http\Controllers\Api\v1\SalesApiController;
use App\Http\Controllers\Api\v1\SupplierApiController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\WarehouseApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Guest Auth route
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // User Management routes
        Route::apiResource('users', UserController::class);

        // Company Management routes
        Route::get('companies', [CompanyApiController::class, 'index']);
        Route::get('companies/{id}/tree', [CompanyApiController::class, 'tree']);
        Route::get('companies/{id}/branches', [CompanyApiController::class, 'branches']);
        Route::get('companies/{id}/stores', [CompanyApiController::class, 'stores']);
        Route::get('companies/{id}/warehouses', [CompanyApiController::class, 'warehouses']);

        // Product Management routes
        Route::get('products', [ProductApiController::class, 'index']);
        Route::get('products/{id}', [ProductApiController::class, 'show']);

        // Supplier Management routes
        Route::get('suppliers', [SupplierApiController::class, 'index']);
        Route::get('suppliers/{id}', [SupplierApiController::class, 'show']);

        // Purchase Management routes
        Route::get('purchase/orders', [PurchaseApiController::class, 'index']);
        Route::post('purchase/grn', [PurchaseApiController::class, 'storeGRN']);

        // Warehouse Management routes
        Route::get('warehouse/bins/{code}', [WarehouseApiController::class, 'binDetails']);
        Route::post('warehouse/adjust', [WarehouseApiController::class, 'adjustStock']);

        // Inventory Management routes
        Route::get('inventory/stock/{sku}', [InventoryApiController::class, 'stockDetails']);
        Route::post('inventory/adjust', [InventoryApiController::class, 'adjustStock']);

        // POS Management routes
        Route::get('pos/customers/{phone}', [POSApiController::class, 'customerDetails']);
        Route::get('pos/giftcards/{cardNumber}', [POSApiController::class, 'giftCardDetails']);

        // Sales Management routes
        Route::get('sales/track/{orderNumber}', [SalesApiController::class, 'trackOrder']);
        Route::post('sales/corporate', [SalesApiController::class, 'createCorporateOrder']);

        // CRM Management routes
        Route::get('crm/customers/{phone}', [CRMApiController::class, 'getProfile']);
        Route::post('crm/tickets', [CRMApiController::class, 'createTicket']);

        // Finance Management routes
        Route::get('finance/statements', [FinanceApiController::class, 'getFinancialStatements']);
        Route::post('finance/journal', [FinanceApiController::class, 'postExternalJournal']);

        // Excise & GST compliance routes
        Route::get('compliance/gst', [ExciseApiController::class, 'getGSTSummary']);
        Route::get('compliance/license/{id}', [ExciseApiController::class, 'checkLicenseStatus']);

        // HRMS routes
        Route::get('hrm/roster', [HRMApiController::class, 'getEmployeeRoster']);
        Route::post('hrm/punch', [HRMApiController::class, 'biometricClockIn']);

        // Delivery Management routes
        Route::get('delivery/assigned', [DeliveryApiController::class, 'getAssignedDeliveries']);
        Route::post('delivery/{id}/gps', [DeliveryApiController::class, 'updateGPSLocation']);
        Route::post('delivery/{id}/checkout', [DeliveryApiController::class, 'confirmOTPCheckout']);

        // Reports & Analytics routes
        Route::get('reports/summary', [ReportApiController::class, 'getSummaryReport']);

        // AI & BI routes
        Route::get('ai/forecast', [AIApiController::class, 'getForecast']);
        Route::post('ai/chat', [AIApiController::class, 'chatQuery']);
    });
});
