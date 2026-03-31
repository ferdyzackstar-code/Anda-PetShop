<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return User::with('roles')->get();
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            $user->getRoleNames()->implode(', ') ?: 'No Role',
            $user->created_at->format('d F Y H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'Roles', 
            'Tanggal Dibuat',
        ];
    }
}
