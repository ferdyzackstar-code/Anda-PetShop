<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    use Importable, SkipsFailures;

    public function isEmpty($row): bool
    {
        return empty($row['name']) && empty($row['price']) && empty($row['stock']);
    }

    public function model(array $row)
    {
        return new Product([
            'name' => $row['name'],
            'category_id' => $row['category_id'],
            'supplier_id' => $row['supplier_id'],
            'outlet_id' => $row['outlet_id'],
            'price' => $row['price'],
            'stock' => $row['stock'],
            'detail' => $row['detail'],
            'status' => 'active',
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|unique:products,name',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'species_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'outlet_id' => 'required|exists:outlets,id',

            // Logika Validasi Silang
            'category_id' => [
                'required',
                function ($attribute, $value, $fail) {

                    $index = explode('.', $attribute)[0];

                    $data = request()->file('file');

                    $category = Category::find($value);

                    if ($category) {
                    }
                },
            ],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'category_id.required' => 'ID Kategori harus diisi.',
            'species_id.exists' => 'ID Species tidak terdaftar di sistem.',
        ];
    }
}
