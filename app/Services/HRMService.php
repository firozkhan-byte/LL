<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\PayrollRecord;
use App\Repositories\Contracts\HRMRepositoryInterface;
use Illuminate\Support\Facades\DB;

class HRMService
{
    protected HRMRepositoryInterface $hrmRepo;

    protected FinanceService $financeService;

    public function __construct(HRMRepositoryInterface $hrmRepo, FinanceService $financeService)
    {
        $this->hrmRepo = $hrmRepo;
        $this->financeService = $financeService;
    }

    public function hireEmployee(array $data)
    {
        return $this->hrmRepo->hireEmployee($data);
    }

    public function punchAttendance(array $data)
    {
        // Auto flag "late" if checked in after 09:30:00
        if (! empty($data['check_in'])) {
            $punchTime = strtotime($data['check_in']);
            $lateLimit = strtotime('09:30:00');
            if ($punchTime > $lateLimit) {
                $data['status'] = 'late';
            } else {
                $data['status'] = 'present';
            }
        }

        return $this->hrmRepo->punchAttendance($data);
    }

    public function createLeave(array $data)
    {
        return $this->hrmRepo->createLeaveRequest($data);
    }

    public function approveLeave(string $leaveId, string $userId): bool
    {
        return $this->hrmRepo->updateLeaveStatus($leaveId, 'approved', $userId);
    }

    public function rejectLeave(string $leaveId, string $userId): bool
    {
        return $this->hrmRepo->updateLeaveStatus($leaveId, 'rejected', $userId);
    }

    public function generatePayroll(string $employeeId, int $month, int $year, float $allowances = 0.00, float $deductions = 0.00)
    {
        return $this->hrmRepo->generatePayroll($employeeId, $month, $year, $allowances, $deductions);
    }

    /**
     * Dispatch payroll payout & create general ledger salary expense writeback.
     */
    public function payoutSalary(string $payrollId): bool
    {
        return DB::transaction(function () use ($payrollId) {
            $payroll = PayrollRecord::find($payrollId);
            if (! $payroll || $payroll->status === 'paid') {
                return false;
            }

            // 1. Mark paid
            $this->hrmRepo->paySalary($payrollId);

            // 2. GL writeback: Debit Utility/Salary Expense (6100), Credit Cash (1010)
            $salaryExpenseAcc = Account::where('code', '6100')->first();
            $cashAcc = Account::where('code', '1010')->first();

            if ($salaryExpenseAcc && $cashAcc) {
                $this->financeService->postJournal([
                    'entry_date' => now()->format('Y-m-d'),
                    'description' => 'Dispatched monthly payroll salary for employee ID: '.$payroll->employee_id,
                    'status' => 'posted',
                    'lines' => [
                        [
                            'account_id' => $salaryExpenseAcc->id,
                            'debit' => $payroll->net_salary,
                            'credit' => 0.00,
                        ],
                        [
                            'account_id' => $cashAcc->id,
                            'debit' => 0.00,
                            'credit' => $payroll->net_salary,
                        ],
                    ],
                ]);
            }

            return true;
        });
    }

    /**
     * Aggregate HR dashboard metrics.
     */
    public function getHRMAnalytics(): array
    {
        $totalEmployeesCount = Employee::where('status', 'active')->count();

        $presentToday = Attendance::whereDate('date', now())
            ->whereIn('status', ['present', 'late'])
            ->count();

        $attendanceRate = $totalEmployeesCount > 0
            ? round(($presentToday / $totalEmployeesCount) * 100, 1)
            : 100.0;

        $activeLeaves = Leave::where('status', 'approved')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->count();

        $monthlyPayrollLiability = PayrollRecord::where('status', 'unpaid')
            ->where('month', intval(date('m')))
            ->where('year', intval(date('Y')))
            ->sum('net_salary');

        return [
            'total_employees' => $totalEmployeesCount,
            'attendance_rate' => $attendanceRate,
            'active_leaves_today' => $activeLeaves,
            'payroll_liability' => $monthlyPayrollLiability,
        ];
    }
}
