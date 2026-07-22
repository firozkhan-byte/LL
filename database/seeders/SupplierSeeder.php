<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. United Spirits Limited
        $uslId = Str::uuid()->toString();
        DB::table('suppliers')->insert([
            'id' => $uslId,
            'name' => 'United Spirits Limited (Diageo India)',
            'code' => 'SUP-2026-0001',
            'gstin' => '27AAACU1234A1Z1',
            'pan' => 'AAACU1234A',
            'payment_terms_days' => 45,
            'credit_limit' => 1500000.00,
            'rating' => 4.80,
            'outstanding_balance' => 350000.00,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('supplier_contacts')->insert([
            [
                'id' => Str::uuid()->toString(),
                'supplier_id' => $uslId,
                'name' => 'Amit Sharma',
                'email' => 'amit.sharma@diageo.com',
                'phone' => '+919876543210',
                'designation' => 'Key Account Manager',
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'supplier_id' => $uslId,
                'name' => 'Sales USL Office',
                'email' => 'sales.india@diageo.com',
                'phone' => '+912266889900',
                'designation' => 'Billing Desk',
                'is_primary' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('supplier_bank_accounts')->insert([
            'id' => Str::uuid()->toString(),
            'supplier_id' => $uslId,
            'bank_name' => 'State Bank of India',
            'account_number' => '10001234567',
            'ifsc_code' => 'SBIN0000001',
            'branch_name' => 'Commercial Branch Mumbai',
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Pernod Ricard India
        $priId = Str::uuid()->toString();
        DB::table('suppliers')->insert([
            'id' => $priId,
            'name' => 'Pernod Ricard India Private Limited',
            'code' => 'SUP-2026-0002',
            'gstin' => '27AABCP5678B2Z2',
            'pan' => 'AABCP5678B',
            'payment_terms_days' => 30,
            'credit_limit' => 1000000.00,
            'rating' => 4.50,
            'outstanding_balance' => 0.00,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('supplier_contacts')->insert([
            'id' => Str::uuid()->toString(),
            'supplier_id' => $priId,
            'name' => 'Rohit Verma',
            'email' => 'rohit.verma@pernod-ricard.com',
            'phone' => '+919876543211',
            'designation' => 'Sales Director',
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('supplier_bank_accounts')->insert([
            'id' => Str::uuid()->toString(),
            'supplier_id' => $priId,
            'bank_name' => 'HDFC Bank',
            'account_number' => '5010023456789',
            'ifsc_code' => 'HDFC0000001',
            'branch_name' => 'Kanjurmarg West',
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. United Breweries Limited
        $ubId = Str::uuid()->toString();
        DB::table('suppliers')->insert([
            'id' => $ubId,
            'name' => 'United Breweries Limited (Kingfisher)',
            'code' => 'SUP-2026-0003',
            'gstin' => '27AAACU9876C3Z3',
            'pan' => 'AAACU9876C',
            'payment_terms_days' => 15,
            'credit_limit' => 800000.00,
            'rating' => 4.20,
            'outstanding_balance' => 120000.00,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('supplier_contacts')->insert([
            'id' => Str::uuid()->toString(),
            'supplier_id' => $ubId,
            'name' => 'Karan Johar',
            'email' => 'karan.johar@unitedbreweries.com',
            'phone' => '+919876543212',
            'designation' => 'Logistics Manager',
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('supplier_bank_accounts')->insert([
            'id' => Str::uuid()->toString(),
            'supplier_id' => $ubId,
            'bank_name' => 'ICICI Bank',
            'account_number' => '000105678901',
            'ifsc_code' => 'ICIC0000001',
            'branch_name' => 'Bandra Kurla Complex',
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Radico Khaitan Limited (Starts pending approval to show approval workflow functionality)
        $rkId = Str::uuid()->toString();
        DB::table('suppliers')->insert([
            'id' => $rkId,
            'name' => 'Radico Khaitan Limited',
            'code' => 'SUP-2026-0004',
            'gstin' => '27AAACR4321D4Z4',
            'pan' => 'AAACR4321D',
            'payment_terms_days' => 60,
            'credit_limit' => 500000.00,
            'rating' => 4.00,
            'outstanding_balance' => 0.00,
            'status' => 'pending_approval',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('supplier_contacts')->insert([
            'id' => Str::uuid()->toString(),
            'supplier_id' => $rkId,
            'name' => 'Sanjay Dutt',
            'email' => 'sanjay.dutt@radico.co.in',
            'phone' => '+919876543213',
            'designation' => 'Regional Coordinator',
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('supplier_bank_accounts')->insert([
            'id' => Str::uuid()->toString(),
            'supplier_id' => $rkId,
            'bank_name' => 'Axis Bank',
            'account_number' => '91234567890',
            'ifsc_code' => 'UTIB0000001',
            'branch_name' => 'Noida Sec 62',
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
