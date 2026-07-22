<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ __('Human Resource Management System (HRMS)') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Maintain employee profiles directory, shift attendances, leave tracking console, and automated double-entry payroll processing.') }}
            </p>
        </div>
        <div class="flex gap-2">
            <button wire:click="openEmployeeModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                {{ __('Hire Employee') }}
            </button>
            <button wire:click="openPunchModal" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ __('Simulate Check-In') }}
            </button>
        </div>
    </div>

    <!-- Alerts -->
    @if (session()->has('emp_success'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-lg flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('emp_success') }}</span>
        </div>
    @endif
    @if (session()->has('att_success'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-lg flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('att_success') }}</span>
        </div>
    @endif
    @if (session()->has('leave_success'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-lg flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('leave_success') }}</span>
        </div>
    @endif
    @if (session()->has('pay_success'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-lg flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('pay_success') }}</span>
        </div>
    @endif

    <!-- HR Analytics metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Staff directory count -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Total Active Roster</span>
            <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-2">{{ $hrmAnalytics['total_employees'] }} Employees</h3>
        </div>

        <!-- Attendance Rate -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Daily Attendance Rate</span>
            <h3 class="text-3xl font-black text-indigo-600 dark:text-indigo-400 font-mono mt-2">{{ $hrmAnalytics['attendance_rate'] }}%</h3>
        </div>

        <!-- Active Leaves today -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Active Leaves Today</span>
            <h3 class="text-3xl font-black text-amber-600 mt-2">{{ $hrmAnalytics['active_leaves_today'] }} Staff</h3>
        </div>

        <!-- Monthly Payroll Liability -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Monthly Payroll Liability</span>
                <h3 class="text-2xl font-black text-indigo-650 dark:text-indigo-400 font-mono mt-1">₹{{ number_format($hrmAnalytics['payroll_liability'], 2) }}</h3>
            </div>
            <button wire:click="openPayrollModal" class="mt-2 text-xs font-bold text-indigo-600 hover:text-indigo-700 block text-left cursor-pointer transition">
                + Generate Employee Paystub
            </button>
        </div>
    </div>

    <!-- Tabs Nav -->
    <div class="border-b border-gray-200 dark:border-gray-700 flex space-x-8 overflow-x-auto">
        <button wire:click="setTab('employees')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'employees' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Employees Directory') }}
        </button>
        <button wire:click="setTab('attendance')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'attendance' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Attendance Logs') }}
        </button>
        <button wire:click="setTab('leaves')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'leaves' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Leave Requests') }}
        </button>
        <button wire:click="setTab('payroll')" class="py-4 text-sm font-bold border-b-2 cursor-pointer transition whitespace-nowrap {{ $activeTab === 'payroll' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
            {{ __('Payroll & General Ledger') }}
        </button>
    </div>

    <!-- Tab Contents -->
    <div class="space-y-6">
        @if ($activeTab === 'employees')
            <!-- Search Directory -->
            <div class="flex bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex-1 max-w-md">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name or email..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                </div>
            </div>

            <!-- Roster Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Employee ID</th>
                                <th class="px-6 py-3">Full Name</th>
                                <th class="px-6 py-3">Email & Contact</th>
                                <th class="px-6 py-3">Dept & Designation</th>
                                <th class="px-6 py-3 text-right">Base Salary</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($employeePaginated as $emp)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors text-sm">
                                    <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $emp->employee_id }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $emp->first_name }} {{ $emp->last_name }}</td>
                                    <td class="px-6 py-4">
                                        <span class="block font-semibold text-gray-800 dark:text-slate-200">{{ $emp->email }}</span>
                                        <span class="block text-xs text-gray-400">{{ $emp->phone }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="block font-bold text-indigo-700 dark:text-indigo-450">{{ $emp->department }}</span>
                                        <span class="block text-xs text-gray-400">{{ $emp->designation }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono font-bold text-gray-900 dark:text-white">₹{{ number_format($emp->salary, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-105 text-green-700 dark:text-green-400">
                                            {{ ucfirst($emp->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No employees found in directory.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div>
                {{ $employeePaginated->links() }}
            </div>
        @endif

        @if ($activeTab === 'attendance')
            <!-- Attendance logs Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Employee</th>
                                <th class="px-6 py-3 text-center">Clock-In</th>
                                <th class="px-6 py-3 text-center">Clock-Out</th>
                                <th class="px-6 py-3 text-center">Biometric Punch Device</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($attendancesList as $att)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors text-sm">
                                    <td class="px-6 py-4 font-mono text-gray-600 dark:text-gray-400">{{ $att->date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $att->employee->first_name }} {{ $att->employee->last_name }}</td>
                                    <td class="px-6 py-4 text-center font-mono text-gray-800 dark:text-slate-200">{{ $att->check_in }}</td>
                                    <td class="px-6 py-4 text-center font-mono text-gray-850 dark:text-slate-200">{{ $att->check_out ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center text-xs text-gray-550 dark:text-slate-400">{{ $att->biometric_device_id ?? 'Web Browser' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 text-xs font-bold uppercase rounded-full
                                            {{ $att->status === 'present' ? 'bg-green-105 text-green-700 dark:text-green-400' : '' }}
                                            {{ $att->status === 'late' ? 'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-400' : '' }}
                                            {{ $att->status === 'absent' ? 'bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-400' : '' }}
                                        ">
                                            {{ $att->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No attendance log rows logged today.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div>
                {{ $attendancesList->links() }}
            </div>
        @endif

        @if ($activeTab === 'leaves')
            <!-- Leave Requests Lists -->
            <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <span class="text-sm font-bold text-gray-900 dark:text-white">Leave Requests Approvals Center</span>
                <button wire:click="openLeaveModal" class="text-xs px-3 py-1.5 bg-indigo-105 hover:bg-indigo-200 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 font-bold rounded cursor-pointer transition">
                    + Apply For Leave
                </button>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Employee</th>
                                <th class="px-6 py-3">Leave Classification</th>
                                <th class="px-6 py-3">Start Date</th>
                                <th class="px-6 py-3">End Date</th>
                                <th class="px-6 py-3">Reason</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-center">Approvals Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($leavesList as $leave)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors text-sm">
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $leave->employee->first_name }} {{ $leave->employee->last_name }}</td>
                                    <td class="px-6 py-4 text-xs font-semibold uppercase block bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 py-0.5 rounded px-2 w-max mt-2">{{ $leave->leave_type }}</td>
                                    <td class="px-6 py-4 font-mono text-gray-600 dark:text-gray-400">{{ $leave->start_date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 font-mono text-gray-600 dark:text-gray-400">{{ $leave->end_date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 text-gray-850 dark:text-slate-200 font-semibold">{{ $leave->reason }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-0.5 text-xs font-bold uppercase rounded-full
                                            {{ $leave->status === 'approved' ? 'bg-green-105 text-green-700 dark:text-green-400' : '' }}
                                            {{ $leave->status === 'pending' ? 'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-400' : '' }}
                                            {{ $leave->status === 'rejected' ? 'bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-400' : '' }}
                                        ">
                                            {{ $leave->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($leave->status === 'pending')
                                            <div class="flex justify-center gap-1.5">
                                                <button wire:click="approveLeave('{{ $leave->id }}')" class="text-xs bg-green-100 hover:bg-green-200 dark:bg-green-950/40 text-green-700 dark:text-green-400 px-2 py-1 font-bold rounded cursor-pointer transition">
                                                    Approve
                                                </button>
                                                <button wire:click="rejectLeave('{{ $leave->id }}')" class="text-xs bg-red-105 hover:bg-red-205 dark:bg-red-950/40 text-red-750 dark:text-red-400 px-2 py-1 font-bold rounded cursor-pointer transition">
                                                    Reject
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Approved by {{ $leave->approver->name ?? 'System' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No leave request records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div>
                {{ $leavesList->links() }}
            </div>
        @endif

        @if ($activeTab === 'payroll')
            <!-- Payroll sheet list -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Pay Period (M/Y)</th>
                                <th class="px-6 py-3">Employee Name</th>
                                <th class="px-6 py-3 text-right">Basic Salary</th>
                                <th class="px-6 py-3 text-right">Allowances</th>
                                <th class="px-6 py-3 text-right">Deductions</th>
                                <th class="px-6 py-3 text-right">Net Salary Payout</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-center">GL Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($payrollList as $payroll)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors text-sm">
                                    <td class="px-6 py-4 font-mono font-bold text-gray-700 dark:text-gray-300">{{ $payroll->month }}/{{ $payroll->year }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</td>
                                    <td class="px-6 py-4 text-right font-mono text-gray-650 dark:text-slate-300">₹{{ number_format($payroll->basic_salary, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-mono text-indigo-650 dark:text-indigo-400">+₹{{ number_format($payroll->allowances, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-mono text-red-500">-₹{{ number_format($payroll->deductions, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-mono font-bold text-gray-950 dark:text-white">₹{{ number_format($payroll->net_salary, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2.5 py-0.5 text-xs font-bold uppercase rounded-full
                                            {{ $payroll->status === 'paid' ? 'bg-green-105 text-green-700 dark:text-green-400' : 'bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400' }}
                                        ">
                                            {{ $payroll->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($payroll->status === 'unpaid')
                                            <button wire:click="paySalary('{{ $payroll->id }}')" class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 font-semibold rounded cursor-pointer transition shadow">
                                                Pay Salary (GL Writeback)
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-500 italic">Dispatched on {{ $payroll->payment_date->format('Y-m-d') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">No payroll entries on record.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div>
                {{ $payrollList->links() }}
            </div>
        @endif
    </div>

    <!-- HIRE EMPLOYEE MODAL -->
    @if ($showingEmployeeModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-md rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Hire New Employee</h3>
                    <button wire:click="$set('showingEmployeeModal', false)" class="text-gray-400 hover:text-gray-650 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">First Name</label>
                            <input type="text" wire:model="firstName" placeholder="First Name" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            @error('firstName') <p class="text-xs text-red-650 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Last Name</label>
                            <input type="text" wire:model="lastName" placeholder="Last Name" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            @error('lastName') <p class="text-xs text-red-650 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Work Email</label>
                        <input type="email" wire:model="email" placeholder="e.g. clerk@liquorerp.in" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        @error('email') <p class="text-xs text-red-650 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Contact Phone</label>
                        <input type="text" wire:model="phone" placeholder="Mobile Number" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        @error('phone') <p class="text-xs text-red-650 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Department</label>
                            <select wire:model="department" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                                <option value="Sales">Sales</option>
                                <option value="Inventory">Inventory</option>
                                <option value="Finance">Finance</option>
                                <option value="Management">Management</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Designation</label>
                            <select wire:model="designation" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                                <option value="POS Billing Clerk">POS Billing Clerk</option>
                                <option value="Store Keeper">Store Keeper</option>
                                <option value="Accounts Manager">Accounts Manager</option>
                                <option value="Warehouse Supervisor">Warehouse Supervisor</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Basic Monthly Salary (₹)</label>
                        <input type="number" wire:model="salary" placeholder="Gross basic salary..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3 text-right">
                        @error('salary') <p class="text-xs text-red-650 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showingEmployeeModal', false)" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg cursor-pointer">Cancel</button>
                    <button type="button" wire:click="saveEmployee" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg cursor-pointer transition-colors shadow">Hire Employee</button>
                </div>
            </div>
        </div>
    @endif

    <!-- BIOMETRIC ATTENDANCE CLOCK IN MODAL -->
    @if ($showingPunchModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-md rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Simulate Biometric Punch Clock</h3>
                    <button wire:click="$set('showingPunchModal', false)" class="text-gray-400 hover:text-gray-650 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Select Employee</label>
                        <select wire:model="punchEmployeeId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            @foreach ($employeesList as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->employee_id }} - {{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Clock-In Time</label>
                            <input type="time" wire:model="punchCheckIn" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Clock-Out Time</label>
                            <input type="time" wire:model="punchCheckOut" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Biometric Terminal Device</label>
                        <input type="text" wire:model="punchDevice" placeholder="e.g. B-MUM-01" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showingPunchModal', false)" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg cursor-pointer">Cancel</button>
                    <button type="button" wire:click="triggerBiometricPunch" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg cursor-pointer transition-colors shadow">Punch Attendance</button>
                </div>
            </div>
        </div>
    @endif

    <!-- GENERATE PAYROLL MODAL -->
    @if ($showingPayrollModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-md rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Generate Monthly Paystub</h3>
                    <button wire:click="$set('showingPayrollModal', false)" class="text-gray-400 hover:text-gray-650 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Target Employee</label>
                        <select wire:model="payEmployeeId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            @foreach ($employeesList as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }} (Basic: ₹{{ number_format($emp->salary, 2) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Allowances (+)</label>
                            <input type="number" step="0.01" min="0" wire:model="payAllowances" placeholder="Allowances" class="w-full rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-1.5 px-3 text-right">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Deductions (-)</label>
                            <input type="number" step="0.01" min="0" wire:model="payDeductions" placeholder="Deductions" class="w-full rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-1.5 px-3 text-right">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showingPayrollModal', false)" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg cursor-pointer">Cancel</button>
                    <button type="button" wire:click="triggerPayrollGeneration" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg cursor-pointer transition-colors shadow">Generate Stub</button>
                </div>
            </div>
        </div>
    @endif

    <!-- SUBMIT LEAVE APPLICATION MODAL -->
    @if ($showingLeaveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 w-full max-w-md rounded-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/40">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Apply For Leave Request</h3>
                    <button wire:click="$set('showingLeaveModal', false)" class="text-gray-400 hover:text-gray-650 dark:hover:text-gray-200 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Employee</label>
                        <select wire:model="leaveEmployeeId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            @foreach ($employeesList as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Leave Category</label>
                        <select wire:model="leaveType" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                            <option value="casual">Casual Leave</option>
                            <option value="sick">Sick Leave</option>
                            <option value="annual">Annual Leave</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Start Date</label>
                            <input type="date" wire:model="leaveStart" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">End Date</label>
                            <input type="date" wire:model="leaveEnd" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Reason Description</label>
                        <textarea wire:model="leaveReason" placeholder="State reason of leave request..." class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-850 text-gray-900 dark:text-gray-100 text-sm py-2 px-3" rows="3"></textarea>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-850/40 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showingLeaveModal', false)" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg cursor-pointer">Cancel</button>
                    <button type="button" wire:click="submitLeave" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg cursor-pointer transition-colors shadow">Apply Request</button>
                </div>
            </div>
        </div>
    @endif
</div>
