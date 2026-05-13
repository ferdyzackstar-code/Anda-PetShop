<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;

class SuppliersImport implements ToCollection, WithHeadingRow
{
    use Importable;

    private array $failures = [];
    private int $importedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; 

            $name = trim((string) ($row['name'] ?? ''));
            $email = trim((string) ($row['email'] ?? ''));
            $city = trim((string) ($row['city'] ?? ''));
            $phone = trim((string) ($row['phone'] ?? ''));
            $address = trim((string) ($row['address'] ?? ''));

            if ($name === '' && $email === '' && $city === '' && $phone === '' && $address === '') {
                continue;
            }

            $errors = [];

            if ($name === '') {
                $errors[] = 'Kolom nama supplier wajib diisi!';
            } elseif (Supplier::where('name', $name)->exists()) {
                $errors[] = "Supplier \"{$name}\" sudah terdaftar di sistem!";
            }

            if ($email === '') {
                $errors[] = 'Kolom email wajib diisi!';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Format email tidak valid!';
            } elseif (Supplier::where('email', $email)->exists()) {
                $errors[] = "Email \"{$email}\" sudah terdaftar di sistem!";
            }

            if ($city === '') {
                $errors[] = 'Kolom kota wajib diisi!';
            }

            if ($phone === '') {
                $errors[] = 'Kolom telepon wajib diisi!';
            } elseif (!ctype_digit($phone)) {
                $errors[] = 'Nomor telepon harus berupa angka!';
            }

            if ($address === '') {
                $errors[] = 'Kolom alamat wajib diisi!';
            }

            if (!empty($errors)) {
                $this->failures[] = "Baris {$rowNumber}: " . implode(', ', $errors);
                continue;
            }

            Supplier::create([
                'name' => $name,
                'email' => $email,
                'city' => $city,
                'phone' => $phone,
                'address' => $address,
                'status' => 'active',
            ]);

            $this->importedCount++;
        }
    }

    public function getFailures(): array
    {
        return $this->failures;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}