<?php

namespace App\Providers;

use App\Repositories\Contracts\ApprovalRepositoryInterface;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Contracts\CRMRepositoryInterface;
use App\Repositories\Contracts\DeliveryRepositoryInterface;
use App\Repositories\Contracts\ExciseRepositoryInterface;
use App\Repositories\Contracts\FinanceRepositoryInterface;
use App\Repositories\Contracts\HRMRepositoryInterface;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use App\Repositories\Contracts\POSRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use App\Repositories\Contracts\SalesRepositoryInterface;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\WarehouseRepositoryInterface;
use App\Repositories\Eloquent\ApprovalRepository;
use App\Repositories\Eloquent\CompanyRepository;
use App\Repositories\Eloquent\CRMRepository;
use App\Repositories\Eloquent\DeliveryRepository;
use App\Repositories\Eloquent\ExciseRepository;
use App\Repositories\Eloquent\FinanceRepository;
use App\Repositories\Eloquent\HRMRepository;
use App\Repositories\Eloquent\InventoryRepository;
use App\Repositories\Eloquent\POSRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\PurchaseRepository;
use App\Repositories\Eloquent\SalesRepository;
use App\Repositories\Eloquent\SupplierRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\WarehouseRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            CompanyRepositoryInterface::class,
            CompanyRepository::class
        );

        $this->app->bind(
            ApprovalRepositoryInterface::class,
            ApprovalRepository::class
        );

        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );

        $this->app->bind(
            SupplierRepositoryInterface::class,
            SupplierRepository::class
        );

        $this->app->bind(
            PurchaseRepositoryInterface::class,
            PurchaseRepository::class
        );

        $this->app->bind(
            WarehouseRepositoryInterface::class,
            WarehouseRepository::class
        );

        $this->app->bind(
            InventoryRepositoryInterface::class,
            InventoryRepository::class
        );

        $this->app->bind(
            POSRepositoryInterface::class,
            POSRepository::class
        );

        $this->app->bind(
            SalesRepositoryInterface::class,
            SalesRepository::class
        );

        $this->app->bind(
            CRMRepositoryInterface::class,
            CRMRepository::class
        );

        $this->app->bind(
            FinanceRepositoryInterface::class,
            FinanceRepository::class
        );

        $this->app->bind(
            ExciseRepositoryInterface::class,
            ExciseRepository::class
        );

        $this->app->bind(
            HRMRepositoryInterface::class,
            HRMRepository::class
        );

        $this->app->bind(
            DeliveryRepositoryInterface::class,
            DeliveryRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (request()->hasHeader('x-forwarded-proto') || env('APP_ENV') === 'production' || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) || is_dir('/tmp')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Implicitly grant "Super Admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
