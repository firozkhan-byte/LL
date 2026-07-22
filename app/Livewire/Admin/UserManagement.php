<?php

namespace App\Livewire\Admin;

use App\Exports\UsersExport;
use App\Imports\UsersImport;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

class UserManagement extends Component
{
    use WithFileUploads, WithPagination;

    // filters & search
    public string $search = '';

    public string $filterRole = '';

    public string $filterStatus = '';

    public string $filterTrashed = '';

    // form state
    public bool $showingUserModal = false;

    public bool $showingLogsModal = false;

    public ?string $userId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $status = 'active';

    public array $selectedRoles = [];

    // file uploads
    public $importFile;

    // activity logs state
    public array $userLogs = [];

    public ?string $logUserName = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterRole' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterTrashed' => ['except' => ''],
    ];

    public function mount(): void
    {
        abort_if(Gate::denies('manage-users'), 403);
    }

    public function render(UserService $userService)
    {
        $users = $userService->getPaginatedUsers([
            'search' => $this->search,
            'role' => $this->filterRole,
            'status' => $this->filterStatus,
            'trashed' => $this->filterTrashed,
        ], 10);

        return view('livewire.admin.user-management', [
            'users' => $users,
            'roles' => Role::all(),
        ])->layout('layouts.app');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterRole(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTrashed(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showingUserModal = true;
    }

    public function openEditModal(string $id, UserService $userService): void
    {
        $this->resetForm();
        $user = $userService->findUser($id);
        if ($user) {
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->status = $user->status;
            $this->selectedRoles = $user->roles->pluck('name')->toArray();
            $this->showingUserModal = true;
        }
    }

    public function saveUser(UserService $userService): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.($this->userId ?? 'NULL').',id',
            'status' => 'required|string|in:active,inactive,suspended',
            'selectedRoles' => 'required|array|min:1',
        ];

        if (! $this->userId) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        $this->validate($rules);

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'roles' => $this->selectedRoles,
        ];

        if (! empty($this->password)) {
            $userData['password'] = $this->password;
        }

        if ($this->userId) {
            $userService->updateUser($this->userId, $userData);
            session()->flash('message', 'User updated successfully.');
        } else {
            $userService->createUser($userData);
            session()->flash('message', 'User created successfully.');
        }

        $this->showingUserModal = false;
        $this->resetForm();
    }

    public function deleteUser(string $id, UserService $userService): void
    {
        $userService->deleteUser($id);
        session()->flash('message', 'User deleted successfully.');
    }

    public function restoreUser(string $id, UserService $userService): void
    {
        $userService->restoreUser($id);
        session()->flash('message', 'User restored successfully.');
    }

    public function viewLogs(string $id): void
    {
        $user = User::withTrashed()->find($id);
        if ($user) {
            $this->logUserName = $user->name;
            $this->userLogs = Activity::query()
                ->where(function ($q) use ($id) {
                    $q->where('causer_id', $id)
                        ->orWhere('subject_id', $id);
                })
                ->orderBy('created_at', 'desc')
                ->take(30)
                ->get()
                ->map(fn ($log) => [
                    'description' => $log->description,
                    'event' => $log->event,
                    'properties' => $log->properties ? json_encode($log->properties) : null,
                    'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                ])
                ->toArray();

            $this->showingLogsModal = true;
        }
    }

    public function exportUsers()
    {
        return Excel::download(new UsersExport, 'users-'.now()->format('Y-m-d').'.xlsx');
    }

    public function importUsers(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        Excel::import(new UsersImport, $this->importFile->getRealPath());

        session()->flash('message', 'Users imported successfully.');
        $this->importFile = null;
    }

    private function resetForm(): void
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->status = 'active';
        $this->selectedRoles = [];
        $this->resetErrorBag();
    }
}
