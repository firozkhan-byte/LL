<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return User::with('roles')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Roles',
            'Status',
            'Last Login At',
            'Last Login IP',
            'Created At',
        ];
    }

    /**
     * @param  mixed  $user
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->roles->pluck('name')->implode(', '),
            $user->status,
            $user->last_login_at ? $user->last_login_at->toDateTimeString() : 'Never',
            $user->last_login_ip ?? 'N/A',
            $user->created_at->toDateTimeString(),
        ];
    }
}
