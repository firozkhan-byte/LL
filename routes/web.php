<?php

use App\Livewire\Admin\UserManagement;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/run-migrations', function () {
    if (request('token') !== env('MIGRATION_TOKEN')) {
        return response('Unauthorized. Please provide the correct token.', 403);
    }
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return 'Migrations completed successfully:<br><pre>' . \Illuminate\Support\Facades\Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return 'Error during migrations: ' . $e->getMessage();
    }
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

use App\Livewire\Admin\ApprovalsInbox;
use App\Livewire\Admin\CompanyManager;
use App\Livewire\Admin\CompanyTree;

Route::get('admin/users', UserManagement::class)
    ->middleware(['auth'])
    ->name('admin.users');

Route::get('admin/company-tree', CompanyTree::class)
    ->middleware(['auth'])
    ->name('admin.company-tree');

Route::get('admin/company-manager', CompanyManager::class)
    ->middleware(['auth'])
    ->name('admin.company-manager');

use App\Livewire\Admin\ProductCatalog;
use App\Livewire\Admin\ProductDetail;

Route::get('admin/approvals', ApprovalsInbox::class)
    ->middleware(['auth'])
    ->name('admin.approvals');

Route::get('admin/products', ProductCatalog::class)
    ->middleware(['auth'])
    ->name('admin.products');

Route::get('admin/products/{id}', ProductDetail::class)
    ->middleware(['auth'])
    ->name('admin.products.detail');

use App\Livewire\Admin\SupplierManager;

Route::get('admin/suppliers', SupplierManager::class)
    ->middleware(['auth'])
    ->name('admin.suppliers');

use App\Livewire\Admin\PurchaseManager;

Route::get('admin/purchase', PurchaseManager::class)
    ->middleware(['auth'])
    ->name('admin.purchase');

use App\Livewire\Admin\WarehouseManager;

Route::get('admin/warehouse', WarehouseManager::class)
    ->middleware(['auth'])
    ->name('admin.warehouse');

use App\Livewire\Admin\InventoryManager;

Route::get('admin/inventory', InventoryManager::class)
    ->middleware(['auth'])
    ->name('admin.inventory');

use App\Livewire\Admin\POSTerminal;

Route::get('admin/pos', POSTerminal::class)
    ->middleware(['auth'])
    ->name('admin.pos');

use App\Livewire\Admin\SalesManager;

Route::get('admin/sales', SalesManager::class)
    ->middleware(['auth'])
    ->name('admin.sales');

use App\Livewire\Admin\CRMManager;

Route::get('admin/crm', CRMManager::class)
    ->middleware(['auth'])
    ->name('admin.crm');

use App\Livewire\Admin\FinanceManager;

Route::get('admin/finance', FinanceManager::class)
    ->middleware(['auth'])
    ->name('admin.finance');

use App\Livewire\Admin\ExciseManager;

Route::get('admin/compliance', ExciseManager::class)
    ->middleware(['auth'])
    ->name('admin.compliance');

use App\Livewire\Admin\HRMManager;

Route::get('admin/hrm', HRMManager::class)
    ->middleware(['auth'])
    ->name('admin.hrm');

use App\Livewire\Admin\DeliveryManager;

Route::get('admin/delivery', DeliveryManager::class)
    ->middleware(['auth'])
    ->name('admin.delivery');

use App\Livewire\Admin\ReportManager;

Route::get('admin/reports', ReportManager::class)
    ->middleware(['auth'])
    ->name('admin.reports');

use App\Livewire\Admin\AIManager;

Route::get('admin/ai', AIManager::class)
    ->middleware(['auth'])
    ->name('admin.ai');

require __DIR__.'/auth.php';
