<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Get all users or apply filters and search with pagination.
     */
    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = User::query();

        // Search filter (name or email)
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if (! empty($filters['role'])) {
            $query->role($filters['role']);
        }

        // Status filter
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Trashed filter
        if (isset($filters['trashed'])) {
            if ($filters['trashed'] === 'only') {
                $query->onlyTrashed();
            } elseif ($filters['trashed'] === 'with') {
                $query->withTrashed();
            }
        }

        // Default ordering
        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Get all users as a collection.
     */
    public function all(): Collection
    {
        return User::all();
    }

    /**
     * Find a user by UUID.
     */
    public function find(string $id): ?User
    {
        return User::find($id);
    }

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update an existing user.
     */
    public function update(string $id, array $data): ?User
    {
        $user = $this->find($id);
        if ($user) {
            $user->update($data);

            return $user;
        }

        return null;
    }

    /**
     * Delete a user (soft delete).
     */
    public function delete(string $id): bool
    {
        $user = $this->find($id);

        return $user ? $user->delete() : false;
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore(string $id): bool
    {
        $user = User::withTrashed()->find($id);

        return $user ? $user->restore() : false;
    }

    /**
     * Force delete a user permanently.
     */
    public function forceDelete(string $id): bool
    {
        $user = User::withTrashed()->find($id);

        return $user ? $user->forceDelete() : false;
    }

    /**
     * Assign a role to a user.
     */
    public function assignRole(string $id, string $role): ?User
    {
        $user = $this->find($id);
        if ($user) {
            $user->assignRole($role);

            return $user;
        }

        return null;
    }

    /**
     * Sync roles for a user.
     */
    public function syncRoles(string $id, array $roles): ?User
    {
        $user = $this->find($id);
        if ($user) {
            $user->syncRoles($roles);

            return $user;
        }

        return null;
    }
}
