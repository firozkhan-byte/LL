<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\PayrollRecord;
use App\Services\HRMService;
use Livewire\Component;
use Livewire\WithPagination;

class HRMManager extends Component
{
    use WithPagination;

    public string $activeTab = 'employees';

    // Filters
    public string $search = '';

    // Hire Employee Form
    public bool $showingEmployeeModal = false;

    public string $firstName = '';

    public string $lastName = '';

    public string $email = '';

    public string $phone = '';

    public string $department = 'Sales';

    public string $designation = 'POS Billing Clerk';

    public float $salary = 0.00;

    // Simulate punch Form
    public bool $showingPunchModal = false;

    public ?string $punchEmployeeId = null;

    public string $punchCheckIn = '09:00:00';

    public string $punchCheckOut = '17:00:00';

    public string $punchDevice = 'B-MUM-01';

    // Leave request Form
    public bool $showingLeaveModal = false;

    public ?string $leaveEmployeeId = null;

    public string $leaveType = 'casual'; // casual, sick, annual

    public string $leaveStart = '';

    public string $leaveEnd = '';

    public string $leaveReason = '';

    // Generate Payroll Form
    public bool $showingPayrollModal = false;

    public ?string $payEmployeeId = null;

    public float $payAllowances = 0.00;

    public float $payDeductions = 0.00;

    protected $queryString = [
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'employees'],
    ];

    public function mount(): void
    {
        $this->leaveStart = now()->format('Y-m-d');
        $this->leaveEnd = now()->format('Y-m-d');
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // --- Hire Employee Operations ---
    public function openEmployeeModal(): void
    {
        $this->firstName = '';
        $this->lastName = '';
        $this->email = '';
        $this->phone = '';
        $this->salary = 0.00;
        $this->showingEmployeeModal = true;
    }

    public function saveEmployee(HRMService $hrmService): void
    {
        $this->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string',
            'department' => 'required|string',
            'designation' => 'required|string',
            'salary' => 'required|numeric|min:1',
        ]);

        $hrmService->hireEmployee([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'department' => $this->department,
            'designation' => $this->designation,
            'salary' => $this->salary,
            'joining_date' => now()->format('Y-m-d'),
            'status' => 'active',
        ]);

        session()->flash('emp_success', 'Hired new employee successfully.');
        $this->showingEmployeeModal = false;
    }

    // --- Attendance Operations ---
    public function openPunchModal(): void
    {
        $this->punchEmployeeId = Employee::first()?->id;
        $this->punchCheckIn = '09:15:00';
        $this->punchCheckOut = '17:30:00';
        $this->showingPunchModal = true;
    }

    public function triggerBiometricPunch(HRMService $hrmService): void
    {
        $this->validate([
            'punchEmployeeId' => 'required|exists:employees,id',
            'punchCheckIn' => 'required',
            'punchCheckOut' => 'required',
            'punchDevice' => 'required|string',
        ]);

        $hrmService->punchAttendance([
            'employee_id' => $this->punchEmployeeId,
            'date' => now()->format('Y-m-d'),
            'check_in' => $this->punchCheckIn,
            'check_out' => $this->punchCheckOut,
            'biometric_device_id' => $this->punchDevice,
        ]);

        session()->flash('att_success', 'Simulated biometric device clock-in register logged.');
        $this->showingPunchModal = false;
    }

    // --- Leave Workflows ---
    public function openLeaveModal(): void
    {
        $this->leaveEmployeeId = Employee::first()?->id;
        $this->leaveStart = now()->format('Y-m-d');
        $this->leaveEnd = now()->format('Y-m-d');
        $this->leaveReason = '';
        $this->showingLeaveModal = true;
    }

    public function submitLeave(HRMService $hrmService): void
    {
        $this->validate([
            'leaveEmployeeId' => 'required|exists:employees,id',
            'leaveType' => 'required|in:casual,sick,annual',
            'leaveStart' => 'required|date',
            'leaveEnd' => 'required|date|after_or_equal:leaveStart',
            'leaveReason' => 'required|string',
        ]);

        $hrmService->createLeave([
            'employee_id' => $this->leaveEmployeeId,
            'leave_type' => $this->leaveType,
            'start_date' => $this->leaveStart,
            'end_date' => $this->leaveEnd,
            'reason' => $this->leaveReason,
        ]);

        session()->flash('leave_success', 'Leave request submitted.');
        $this->showingLeaveModal = false;
    }

    public function approveLeave(string $leaveId, HRMService $hrmService): void
    {
        $hrmService->approveLeave($leaveId, auth()->id());
        session()->flash('leave_success', 'Leave request approved.');
    }

    public function rejectLeave(string $leaveId, HRMService $hrmService): void
    {
        $hrmService->rejectLeave($leaveId, auth()->id());
        session()->flash('leave_success', 'Leave request rejected.');
    }

    // --- Payroll Operations ---
    public function openPayrollModal(): void
    {
        $this->payEmployeeId = Employee::first()?->id;
        $this->payAllowances = 0.00;
        $this->payDeductions = 0.00;
        $this->showingPayrollModal = true;
    }

    public function triggerPayrollGeneration(HRMService $hrmService): void
    {
        $this->validate([
            'payEmployeeId' => 'required|exists:employees,id',
            'payAllowances' => 'required|numeric|min:0',
            'payDeductions' => 'required|numeric|min:0',
        ]);

        $hrmService->generatePayroll(
            $this->payEmployeeId,
            intval(date('m')),
            intval(date('Y')),
            $this->payAllowances,
            $this->payDeductions
        );

        session()->flash('pay_success', 'Payroll slip generated.');
        $this->showingPayrollModal = false;
    }

    public function paySalary(string $payrollId, HRMService $hrmService): void
    {
        $hrmService->payoutSalary($payrollId);
        session()->flash('pay_success', 'Salary net amount paid out. General ledger entries written.');
    }

    public function render(HRMService $hrmService)
    {
        $employeesList = Employee::orderBy('first_name')->get();
        $employeePaginated = [];
        $attendancesList = [];
        $leavesList = [];
        $payrollList = [];
        $hrmAnalytics = $hrmService->getHRMAnalytics();

        if ($this->activeTab === 'employees') {
            $employeePaginated = Employee::when($this->search, function ($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                    ->orWhere('last_name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            })
                ->orderBy('first_name')
                ->paginate(10);
        } elseif ($this->activeTab === 'attendance') {
            $attendancesList = Attendance::with('employee')->orderBy('date', 'desc')->paginate(10);
        } elseif ($this->activeTab === 'leaves') {
            $leavesList = Leave::with(['employee', 'approver'])->orderBy('created_at', 'desc')->paginate(10);
        } elseif ($this->activeTab === 'payroll') {
            $payrollList = PayrollRecord::with('employee')->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(10);
        }

        return view('livewire.admin.h-r-m-manager', [
            'employeesList' => $employeesList,
            'employeePaginated' => $employeePaginated,
            'attendancesList' => $attendancesList,
            'leavesList' => $leavesList,
            'payrollList' => $payrollList,
            'hrmAnalytics' => $hrmAnalytics,
        ])->layout('layouts.app');
    }
}
