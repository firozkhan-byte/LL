<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\HRMManager;
use App\Models\Account;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\PayrollRecord;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HRMManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Super Admin');

        // Create standard Chart of Accounts for payroll mapping
        Account::create(['code' => '6100', 'name' => 'Salary Expense', 'type' => 'expense']);
        Account::create(['code' => '1010', 'name' => 'Cash Account', 'type' => 'asset']);

        $this->employee = Employee::create([
            'employee_id' => 'EMP-2026-9999',
            'first_name' => 'John',
            'last_name' => 'HRM Doe',
            'email' => 'john.hrm@liquorerp.in',
            'phone' => '9555500000',
            'department' => 'Sales',
            'designation' => 'Clerk',
            'joining_date' => '2026-01-01',
            'salary' => 20000.00,
            'status' => 'active',
        ]);
    }

    public function test_hrm_manager_component_can_be_rendered(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.hrm'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(HRMManager::class);
    }

    public function test_can_hire_employee(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(HRMManager::class)
            ->call('openEmployeeModal')
            ->set('firstName', 'Jane')
            ->set('lastName', 'HRM Clerk')
            ->set('email', 'jane.hrm@liquorerp.in')
            ->set('phone', '9444400000')
            ->set('department', 'Sales')
            ->set('designation', 'POS Billing Clerk')
            ->set('salary', 25000.00)
            ->call('saveEmployee')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('employees', [
            'first_name' => 'Jane',
            'email' => 'jane.hrm@liquorerp.in',
        ]);
    }

    public function test_can_punch_attendance(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(HRMManager::class)
            ->call('openPunchModal')
            ->set('punchEmployeeId', $this->employee->id)
            ->set('punchCheckIn', '09:00:00')
            ->set('punchCheckOut', '17:00:00')
            ->set('punchDevice', 'B-MUM-01')
            ->call('triggerBiometricPunch')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->employee->id,
            'check_in' => '09:00:00',
            'status' => 'present',
        ]);
    }

    public function test_can_submit_and_approve_leave(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(HRMManager::class)
            ->call('openLeaveModal')
            ->set('leaveEmployeeId', $this->employee->id)
            ->set('leaveType', 'casual')
            ->set('leaveStart', '2026-07-20')
            ->set('leaveEnd', '2026-07-22')
            ->set('leaveReason', 'Sick leave request')
            ->call('submitLeave')
            ->assertHasNoErrors();

        $leave = Leave::first();
        $this->assertEquals('pending', $leave->status);

        Livewire::actingAs($this->adminUser)
            ->test(HRMManager::class)
            ->call('approveLeave', $leave->id)
            ->assertHasNoErrors();

        $this->assertEquals('approved', $leave->fresh()->status);
    }

    public function test_can_generate_and_payout_payroll_slip_with_gl_journal(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(HRMManager::class)
            ->call('openPayrollModal')
            ->set('payEmployeeId', $this->employee->id)
            ->set('payAllowances', 1000.00)
            ->set('payDeductions', 500.00)
            ->call('triggerPayrollGeneration')
            ->assertHasNoErrors();

        $payroll = PayrollRecord::first();
        $this->assertEquals('unpaid', $payroll->status);
        $this->assertEquals(20500.00, $payroll->net_salary);

        Livewire::actingAs($this->adminUser)
            ->test(HRMManager::class)
            ->call('paySalary', $payroll->id)
            ->assertHasNoErrors();

        $this->assertEquals('paid', $payroll->fresh()->status);

        // Verify General Ledger double-entry writeback journal lines
        $this->assertDatabaseHas('journal_entries', [
            'status' => 'posted',
        ]);
    }
}
