<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

class ProductsImport implements ToModel, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures;

    private int $currentRow = 0;
    private int $importedCount = 0;

    public function model(array $row)
    {
        $this->currentRow++;
        $rowNumber = $this->currentRow + 1; 

        try {
            $name = trim($row['name'] ?? '');
            $price = trim($row['price'] ?? '');
            $stock = trim($row['stock'] ?? '');
            $speciesId = trim($row['species_id'] ?? '');
            $categoryId = trim($row['category_id'] ?? '');
            $detail = trim($row['detail'] ?? '');

            if (empty($name) && empty($price) && empty($stock) && empty($speciesId) && empty($categoryId)) {
                return null;
            }

            if (empty($name)) {
                throw new \Exception('Nama produk wajib diisi');
            }

            if (strlen($name) > 255) {
                throw new \Exception('Nama produk maksimal 255 karakter');
            }

            if (Product::where('name', $name)->exists()) {
                throw new \Exception("Produk \"{$name}\" sudah terdaftar");
            }

            if (empty($price)) {
                throw new \Exception('Harga wajib diisi');
            }

            if (!is_numeric($price) || (float) $price < 0) {
                throw new \Exception('Harga harus angka positif');
            }

            if (empty($stock)) {
                throw new \Exception('Stok wajib diisi');
            }

            if (!ctype_digit($stock) || (int) $stock < 0) {
                throw new \Exception('Stok harus bilangan bulat non-negatif');
            }

            if (empty($speciesId)) {
                throw new \Exception('Species ID wajib diisi');
            }

            if (!Category::where('id', (int) $speciesId)->exists()) {
                throw new \Exception("Species ID \"{$speciesId}\" tidak terdaftar");
            }

            if (empty($categoryId)) {
                throw new \Exception('Kategori ID wajib diisi');
            }

            $category = Category::find((int) $categoryId);
            if (!$category) {
                throw new \Exception("Kategori ID \"{$categoryId}\" tidak ditemukan");
            }

            if (is_null($category->parent_id)) {
                throw new \Exception('Gunakan Kategori ID (bukan Species ID)');
            }

            $this->importedCount++;

            return new Product([
                'name' => $name,
                'category_id' => (int) $categoryId,
                'price' => (float) $price,
                'stock' => (int) $stock,
                'detail' => $detail ?: null,
                'status' => 'active',
            ]);
        } catch (\Exception $e) {
            $this->onFailure(new Failure($rowNumber, 'data', ["Baris {$rowNumber}: {$e->getMessage()}"]));
            return null;
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
