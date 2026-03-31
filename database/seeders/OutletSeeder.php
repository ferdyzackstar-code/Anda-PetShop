<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Outlet;

class OutletSeeder extends Seeder
{
    public function run(): void
    {
        $outlets = [['name' => 'Anda Petshop Kemanggisan', 'phone' => '081234567890', 'address' => 'Jakarta Barat'], ['name' => 'Anda Petshop Cipinang', 'phone' => '081234567891', 'address' => 'Jakarta Timur'], ['name' => 'Anda Petshop Pengadegan', 'phone' => '081234567892', 'address' => 'Jakarta Selatan'], ['name' => 'Anda Petshop Rawa Belong', 'phone' => '081234567893', 'address' => 'Jakarta Barat'], ['name' => 'Anda Petshop Tambun Utara', 'phone' => '081234567894', 'address' => 'Kabupaten Bekasi']];

        foreach ($outlets as $outlet) {
            Outlet::create($outlet);
        }
    }
}
