<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OutletSeeder extends Seeder
{
    public function run()
    {
        $outlets = [['name' => 'Anda Petshop Kemanggisan', 'address' => 'Jakarta Barat'], ['name' => 'Anda Petshop Cipinang', 'address' => 'Jakarta Timur'], ['name' => 'Anda Petshop Pengadegan', 'address' => 'Jakarta Selatan'], ['name' => 'Anda Petshop Rawa Belong', 'address' => 'Jakarta Barat']];

        foreach ($outlets as $outlet) {
            \App\Models\Outlet::create($outlet);
        }
    }
}
