<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyId = Str::uuid()->toString();
        $regionalOfficeId = Str::uuid()->toString();
        $branchMumbaiId = Str::uuid()->toString();
        $branchPuneId = Str::uuid()->toString();
        $buRetailId = Str::uuid()->toString();
        $buWholesaleId = Str::uuid()->toString();

        // 1. Company
        DB::table('companies')->insert([
            'id' => $companyId,
            'name' => 'Living Liquidz Retail Ltd',
            'registration_number' => 'U51228MH2002PLC137943',
            'tax_number' => '27AAACL3045F1Z9',
            'email' => 'corporate@livingliquidz.com',
            'phone' => '+91 22 6633 4455',
            'website' => 'https://livingliquidz.com',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Company Settings
        DB::table('company_settings')->insert([
            'id' => Str::uuid()->toString(),
            'company_id' => $companyId,
            'currency' => 'INR',
            'timezone' => 'Asia/Kolkata',
            'fiscal_year_start' => '04-01',
            'address_line1' => 'Living Liquidz Corporate Office, Nariman Point',
            'address_line2' => 'Express Towers, 14th Floor',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'postal_code' => '400021',
            'country' => 'India',
            'settings' => json_encode(['enable_excise_checks' => true, 'default_tax_rate' => 18.0]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Regional Office
        DB::table('regional_offices')->insert([
            'id' => $regionalOfficeId,
            'company_id' => $companyId,
            'name' => 'Western India Division',
            'code' => 'RO-WEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Branches
        DB::table('branches')->insert([
            [
                'id' => $branchMumbaiId,
                'company_id' => $companyId,
                'regional_office_id' => $regionalOfficeId,
                'name' => 'Mumbai Central Branch',
                'code' => 'BR-MUM-01',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => $branchPuneId,
                'company_id' => $companyId,
                'regional_office_id' => $regionalOfficeId,
                'name' => 'Pune Regional Hub',
                'code' => 'BR-PUN-02',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 5. Departments
        DB::table('departments')->insert([
            [
                'id' => Str::uuid()->toString(),
                'company_id' => $companyId,
                'name' => 'Operations & Retail',
                'code' => 'DEPT-OPS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'company_id' => $companyId,
                'name' => 'Finance & Excise',
                'code' => 'DEPT-FIN',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'company_id' => $companyId,
                'name' => 'Human Resources',
                'code' => 'DEPT-HR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 6. Business Units
        DB::table('business_units')->insert([
            [
                'id' => $buRetailId,
                'company_id' => $companyId,
                'name' => 'Retail Stores Business Unit',
                'code' => 'BU-RETAIL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => $buWholesaleId,
                'company_id' => $companyId,
                'name' => 'Institutional Sales & Wholesale',
                'code' => 'BU-WHOLESALE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 7. Cost Centers
        DB::table('cost_centers')->insert([
            [
                'id' => Str::uuid()->toString(),
                'company_id' => $companyId,
                'business_unit_id' => $buRetailId,
                'name' => 'Bandra West Store Cost Center',
                'code' => 'CC-BND-RETAIL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'company_id' => $companyId,
                'business_unit_id' => $buWholesaleId,
                'name' => 'Western region Depot Cost Center',
                'code' => 'CC-WEST-DEPOT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 8. Stores
        DB::table('stores')->insert([
            [
                'id' => Str::uuid()->toString(),
                'branch_id' => $branchMumbaiId,
                'name' => 'Living Liquidz Bandra Store',
                'code' => 'ST-MUM-BND',
                'license_number' => 'EX-MUM-2026-004',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'branch_id' => $branchMumbaiId,
                'name' => 'Living Liquidz Andheri Store',
                'code' => 'ST-MUM-AND',
                'license_number' => 'EX-MUM-2026-018',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'branch_id' => $branchPuneId,
                'name' => 'Living Liquidz Pune Camp Store',
                'code' => 'ST-PUN-CMP',
                'license_number' => 'EX-PUN-2026-081',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 9. Warehouses
        DB::table('warehouses')->insert([
            [
                'id' => Str::uuid()->toString(),
                'branch_id' => $branchMumbaiId,
                'name' => 'Mumbai Central Warehouse',
                'code' => 'WH-MUM-CTR',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'branch_id' => $branchPuneId,
                'name' => 'Pune Regional Depot',
                'code' => 'WH-PUN-DEP',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Get admin user ID for approval seeder
        $adminUserId = DB::table('users')->where('email', 'admin@livingliquidz.com')->value('id');

        if ($adminUserId) {
            // 10. Sample Pending Approvals
            DB::table('approvals')->insert([
                'id' => Str::uuid()->toString(),
                'approvable_type' => 'App\\Models\\Store',
                'approvable_id' => null, // null since it's a creation request
                'action' => 'create',
                'data' => json_encode([
                    'name' => 'Living Liquidz Colaba Store',
                    'code' => 'ST-MUM-CLB',
                    'license_number' => 'EX-MUM-2026-099',
                    'branch_id' => $branchMumbaiId,
                    'status' => 'pending_approval',
                ]),
                'status' => 'pending',
                'requested_by' => $adminUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
