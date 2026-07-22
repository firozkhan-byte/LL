<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HRMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = DB::table('users')->first();

        // 1. Seed Employee
        $empId = Str::uuid()->toString();
        DB::table('employees')->insert([
            'id' => $empId,
            'employee_id' => 'EMP-2026-0001',
            'user_id' => $user?->id,
            'first_name' => 'Rajesh',
            'last_name' => 'Kumar',
            'email' => 'rajesh@liquorerp.in',
            'phone' => '9888800000',
            'department' => 'Sales',
            'designation' => 'POS Billing Clerk',
            'joining_date' => now()->subMonths(10)->format('Y-m-d'),
            'salary' => 30000.00,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Seed Attendance
        DB::table('attendances')->insert([
            'id' => Str::uuid()->toString(),
            'employee_id' => $empId,
            'date' => now()->format('Y-m-d'),
            'check_in' => '09:15:00',
            'check_out' => '18:00:00',
            'status' => 'present',
            'biometric_device_id' => 'B-MUM-01',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Seed Leave
        DB::table('leaves')->insert([
            'id' => Str::uuid()->toString(),
            'employee_id' => $empId,
            'leave_type' => 'casual',
            'start_date' => now()->subDays(5)->format('Y-m-d'),
            'end_date' => now()->subDays(4)->format('Y-m-d'),
            'reason' => 'Family ceremony function',
            'status' => 'approved',
            'approved_by' => $user?->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Seed unpaid Payroll
        DB::table('payroll_records')->insert([
            'id' => Str::uuid()->toString(),
            'employee_id' => $empId,
            'payment_date' => null,
            'month' => intval(date('m')),
            'year' => intval(date('Y')),
            'basic_salary' => 30000.00,
            'allowances' => 2500.00,
            'deductions' => 1000.00,
            'net_salary' => 31500.00,
            'status' => 'unpaid',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
