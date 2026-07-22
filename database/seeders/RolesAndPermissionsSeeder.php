<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            // Auth & User Management
            'manage-users',
            'manage-roles',
            'view-activity-logs',

            // Company Management
            'manage-company',

            // Product Management
            'manage-products',
            'view-products',

            // Supplier Management
            'manage-suppliers',

            // Purchase Management
            'manage-purchases',

            // Warehouse Management
            'manage-warehouse',

            // Inventory Management
            'manage-inventory',

            // POS System
            'manage-pos',
            'use-pos',

            // Sales Management
            'manage-sales',

            // Customer CRM
            'manage-crm',

            // Finance
            'manage-finance',

            // GST & Excise Compliance
            'manage-compliance',

            // HRMS
            'manage-hrms',

            // Delivery Management
            'manage-delivery',

            // Reports & Analytics
            'view-reports',
            'export-reports',

            // AI & BI
            'view-bi-dashboard',
            'use-ai-assistant',
        ];

        foreach ($permissions as $permissionName) {
            Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // 2. Create Roles and Assign Permissions

        // Super Admin (has all permissions)
        $superAdminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        // Spatie allows super admin role check via Gate::before, but we can assign all permissions as well.
        $superAdminRole->givePermissionTo(Permission::all());

        // CEO / Executives (most permissions including reports and BI)
        $ceoRole = Role::create(['name' => 'CEO', 'guard_name' => 'web']);
        $ceoRole->givePermissionTo([
            'view-reports', 'export-reports', 'view-bi-dashboard', 'use-ai-assistant',
            'manage-company', 'view-products', 'view-activity-logs',
        ]);

        // Regional Manager
        $regionalManagerRole = Role::create(['name' => 'Regional Manager', 'guard_name' => 'web']);
        $regionalManagerRole->givePermissionTo([
            'view-reports', 'export-reports', 'view-products',
            'manage-warehouse', 'manage-inventory', 'manage-sales',
        ]);

        // Store Manager
        $storeManagerRole = Role::create(['name' => 'Store Manager', 'guard_name' => 'web']);
        $storeManagerRole->givePermissionTo([
            'use-pos', 'manage-pos', 'manage-sales', 'manage-inventory',
            'view-products', 'view-reports', 'manage-crm',
        ]);

        // Warehouse Manager
        $warehouseManagerRole = Role::create(['name' => 'Warehouse Manager', 'guard_name' => 'web']);
        $warehouseManagerRole->givePermissionTo([
            'manage-warehouse', 'manage-inventory', 'view-products',
        ]);

        // Cashier
        $cashierRole = Role::create(['name' => 'Cashier', 'guard_name' => 'web']);
        $cashierRole->givePermissionTo([
            'use-pos', 'view-products',
        ]);

        // Sales Executive
        $salesExecRole = Role::create(['name' => 'Sales Executive', 'guard_name' => 'web']);
        $salesExecRole->givePermissionTo([
            'use-pos', 'view-products', 'manage-crm',
        ]);

        // Inventory Manager
        $inventoryManagerRole = Role::create(['name' => 'Inventory Manager', 'guard_name' => 'web']);
        $inventoryManagerRole->givePermissionTo([
            'manage-inventory', 'view-products', 'manage-warehouse',
        ]);

        // Finance Manager
        $financeManagerRole = Role::create(['name' => 'Finance Manager', 'guard_name' => 'web']);
        $financeManagerRole->givePermissionTo([
            'manage-finance', 'view-reports', 'export-reports',
        ]);

        // HR Manager
        $hrManagerRole = Role::create(['name' => 'HR Manager', 'guard_name' => 'web']);
        $hrManagerRole->givePermissionTo([
            'manage-hrms', 'view-reports',
        ]);

        // Marketing Manager
        $marketingManagerRole = Role::create(['name' => 'Marketing Manager', 'guard_name' => 'web']);
        $marketingManagerRole->givePermissionTo([
            'manage-crm', 'view-reports', 'view-bi-dashboard',
        ]);

        // Delivery Executive
        $deliveryExecRole = Role::create(['name' => 'Delivery Executive', 'guard_name' => 'web']);
        $deliveryExecRole->givePermissionTo([
            'manage-delivery',
        ]);

        // 3. Create Default Users and Assign Roles

        // Super Admin User
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@livingliquidz.com',
            'password' => Hash::make('Password123'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $admin->assignRole($superAdminRole);

        // Store Manager User
        $manager = User::create([
            'name' => 'John Manager',
            'email' => 'manager@livingliquidz.com',
            'password' => Hash::make('Password123'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $manager->assignRole($storeManagerRole);

        // Cashier User
        $cashier = User::create([
            'name' => 'Jane Cashier',
            'email' => 'cashier@livingliquidz.com',
            'password' => Hash::make('Password123'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $cashier->assignRole($cashierRole);
    }
}
