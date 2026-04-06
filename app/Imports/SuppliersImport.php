<?php

namespace App\Imports;

use App\Models\Supplier;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure; 
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class SuppliersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        $supplier = new Supplier([
            'name'     => $row['name'],
            'email'    => $row['email'],
            'city'    => $row['city'],
            'phone'    => $row['phone'],
            'address'    => $row['address'],
            'status'    => 'active',
        ]);

        return $supplier;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|unique:suppliers,name',
            'email'    => 'required|email|unique:suppliers,email',
            'city' => 'required',
            'phone' => 'required|integer',
            'address' => 'required',
        ];
    }
}