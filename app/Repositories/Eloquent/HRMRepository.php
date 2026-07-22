<?php

namespace App\Repositories\Eloquent;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\PayrollRecord;
use App\Repositories\Contracts\HRMRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class HRMRepository implements HRMRepositoryInterface
{
    public function hireEmployee(array $data): Employee
    {
        return Employee::create($data);
    }

    public function punchAttendance(array $data): Attendance
    {
        return Attendance::updateOrCreate(
            ['employee_id' => $data['employee_id'], 'date' => $data['date']],
            [
                'check_in' => $data['check_in'] ?? null,
                'check_out' => $data['check_out'] ?? null,
                'status' => $data['status'] ?? 'present',
                'biometric_device_id' => $data['biometric_device_id'] ?? null,
            ]
        );
    }

    public function getAttendances(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Attendance::with('employee');

        if (! empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }
        if (! empty($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }

        return $query->orderBy('date', 'desc')->paginate($perPage);
    }

    public function createLeaveRequest(array $data): Leave
    {
        $data['status'] = 'pending';

        return Leave::create($data);
    }

    public function updateLeaveStatus(string $leaveId, string $status, string $userId): bool
    {
        $leave = Leave::find($leaveId);
        if (! $leave) {
            return false;
        }

        return $leave->update([
            'status' => $status,
            'approved_by' => $userId,
        ]);
    }

    public function getLeaves(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Leave::with(['employee', 'approver']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function generatePayroll(string $employeeId, int $month, int $year, float $allowances, float $deductions): PayrollRecord
    {
        $emp = Employee::findOrFail($employeeId);
        $basic = $emp->salary;
        $net = $basic + $allowances - $deductions;

        return PayrollRecord::updateOrCreate(
            ['employee_id' => $employeeId, 'month' => $month, 'year' => $year],
            [
                'basic_salary' => $basic,
                'allowances' => $allowances,
                'deductions' => $deductions,
                'net_salary' => $net,
                'status' => 'unpaid',
            ]
        );
    }

    public function paySalary(string $payrollId): bool
    {
        $rec = PayrollRecord::find($payrollId);
        if (! $rec) {
            return false;
        }

        return $rec->update([
            'payment_date' => now()->format('Y-m-d'),
            'status' => 'paid',
        ]);
    }

    public function getPayrollRecords(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = PayrollRecord::with('employee');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate($perPage);
    }
}
