<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    /**
     * Get all users or apply filters and search with pagination.
     */
    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Get all users as a collection.
     */
    public function all(): Collection;

    /**
     * Find a user by UUID.
     */
    public function find(string $id): ?User;

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Create a new user.
     */
    public function create(array $data): User;

    /**
     * Update an existing user.
     */
    public function update(string $id, array $data): ?User;

    /**
     * Delete a user (soft delete).
     */
    public function delete(string $id): bool;

    /**
     * Restore a soft-deleted user.
     */
    public function restore(string $id): bool;

    /**
     * Force delete a user permanently.
     */
    public function forceDelete(string $id): bool;

    /**
     * Assign a role to a user.
     */
    public function assignRole(string $id, string $role): ?User;

    /**
     * Sync roles for a user.
     */
    public function syncRoles(string $id, array $roles): ?User;
}
