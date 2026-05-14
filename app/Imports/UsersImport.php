<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    use SkipsFailures;

    private int $importedCount = 0;

    public function model(array $row)
    {
        $user = new User([
            'name' => $row['name'],
            'email' => $row['email'],
            'password' => Hash::make($row['password']),
        ]);

        $user->assignRole('User');
        $this->importedCount++;
        return $user;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:5|max:100',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'name.required' => 'Nama pengguna wajib diisi',
            'name.string' => 'Nama pengguna harus berupa teks',
            'name.max' => 'Nama pengguna maksimal 100 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar di sistem',
            'password.required' => 'Password wajib diisi',
            'password.string' => 'Password harus berupa teks',
            'password.min' => 'Password minimal 5 karakter',
            'password.max' => 'Password maksimal 100 karakter',
        ];
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
