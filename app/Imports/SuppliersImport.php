<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

class SuppliersImport implements ToModel, WithHeadingRow, SkipsOnFailure
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
            $email = trim($row['email'] ?? '');
            $city = trim($row['city'] ?? '');
            $phone = trim($row['phone'] ?? '');
            $address = trim($row['address'] ?? '');

            if (empty($name) && empty($email) && empty($city) && empty($phone) && empty($address)) {
                return null;
            }

            if (empty($name)) {
                throw new \Exception('Nama supplier wajib diisi');
            }

            if (strlen($name) > 100) {
                throw new \Exception('Nama supplier maksimal 100 karakter');
            }

            if (Supplier::where('name', $name)->exists()) {
                throw new \Exception("Supplier \"{$name}\" sudah terdaftar");
            }

            if (empty($email)) {
                throw new \Exception('Email wajib diisi');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Format email tidak valid');
            }

            if (Supplier::where('email', $email)->exists()) {
                throw new \Exception("Email \"{$email}\" sudah terdaftar");
            }

            if (empty($city)) {
                throw new \Exception('Kota wajib diisi');
            }

            if (strlen($city) > 100) {
                throw new \Exception('Nama kota maksimal 100 karakter');
            }

            if (empty($phone)) {
                throw new \Exception('Nomor telepon wajib diisi');
            }

            if (!ctype_digit($phone)) {
                throw new \Exception('Nomor telepon hanya boleh berisi angka');
            }

            if (strlen($phone) < 8 || strlen($phone) > 15) {
                throw new \Exception('Nomor telepon harus 8-15 digit');
            }

            if (empty($address)) {
                throw new \Exception('Alamat wajib diisi');
            }

            if (strlen($address) > 100) {
                throw new \Exception('Alamat maksimal 100 karakter');
            }

            $this->importedCount++;

            return new Supplier([
                'name' => $name,
                'email' => $email,
                'city' => $city,
                'phone' => $phone,
                'address' => $address,
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
