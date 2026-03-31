<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Penting

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Debugging: Jika masih tidak masuk, kita paksa error untuk intip isinya
        // dd($row);

        return new User([
            'name' => $row['name'],
            'email' => $row['email'],
            'password' => Hash::make($row['password']),
        ]);
    }
}
