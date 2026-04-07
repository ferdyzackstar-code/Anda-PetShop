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
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows, WithMultipleSheets
{
    use Importable, SkipsFailures;

    public function sheets(): array
    {
        return [
            0 => new self(),
        ];
    }

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

            'category_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $category = Category::find($value);

                    if (!$category) {
                        $fail('ID Kategori tidak ditemukan di sistem.');
                        return;
                    }

                    if (is_null($category->parent_id)) {
                        $fail('ID yang dimasukkan adalah ID Species. Harap masukkan ID Sub-Kategori (contoh: Makanan/Kandang).');
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
