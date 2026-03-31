<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'PT Petindo Jaya Abadi',
                'phone' => '021-5551234',
                'address' => 'Kawasan Industri Jababeka, Jl. Tekno Raya No. 1',
                'email' => 'sales@petindojaya.co.id',
                'city' => 'Cikarang',
                'status' => 'active',
            ],
            [
                'name' => 'PT Aneka Satwa Nusantara',
                'phone' => '021-8889990',
                'address' => 'Jl. Daan Mogot KM 15, Kalideres',
                'email' => 'info@anekasatwa.id',
                'city' => 'Jakarta Barat',
                'status' => 'active',
            ],
            [
                'name' => 'CV Fauna Medika Sentosa',
                'phone' => '022-4443211',
                'address' => 'Jl. Pasteur No. 45',
                'email' => 'contact@faunamedika.com',
                'city' => 'Bandung',
                'status' => 'active',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
