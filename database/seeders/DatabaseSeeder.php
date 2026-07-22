<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            CompanySeeder::class,
            ProductSeeder::class,
            SupplierSeeder::class,
            PurchaseSeeder::class,
            WarehouseSeeder::class,
            InventorySeeder::class,
            POSSeeder::class,
            SalesSeeder::class,
            CRMSeeder::class,
            FinanceSeeder::class,
            ExciseSeeder::class,
            HRMSeeder::class,
            DeliverySeeder::class,
        ]);
    }
}
