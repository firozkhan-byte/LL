<?php

namespace App\Repositories\Contracts;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\PayrollRecord;
use Illuminate\Pagination\LengthAwarePaginator;

interface HRMRepositoryInterface
{
    public function hireEmployee(array $data): Employee;

    public function punchAttendance(array $data): Attendance;

    public function getAttendances(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function createLeaveRequest(array $data): Leave;

    public function updateLeaveStatus(string $leaveId, string $status, string $userId): bool;

    public function getLeaves(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function generatePayroll(string $employeeId, int $month, int $year, float $allowances, float $deductions): PayrollRecord;

    public function paySalary(string $payrollId): bool;

    public function getPayrollRecords(array $filters, int $perPage = 10): LengthAwarePaginator;
}
