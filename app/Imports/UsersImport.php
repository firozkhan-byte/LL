<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
     * @return Model|null
     */
    public function model(array $row)
    {
        if (empty($row['email'])) {
            return null;
        }

        // Check if user already exists
        if (User::where('email', $row['email'])->exists()) {
            return null;
        }

        $user = User::create([
            'name' => $row['name'] ?? 'Imported User',
            'email' => $row['email'],
            'password' => Hash::make($row['password'] ?? 'Password123'),
            'status' => $row['status'] ?? 'active',
            'email_verified_at' => now(),
        ]);

        $roles = isset($row['roles']) ? explode(',', $row['roles']) : ['Cashier'];
        foreach ($roles as $roleName) {
            $user->assignRole(trim($roleName));
        }

        return $user;
    }
}
