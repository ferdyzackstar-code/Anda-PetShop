<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure; 
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        $user = new User([
            'name'     => $row['name'],
            'email'    => $row['email'],
            'password' => Hash::make($row['password']),
        ]);

        $user->assignRole('User');
        return $user;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required',
        ];
    }
}