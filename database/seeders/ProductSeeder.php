<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Outlet;
use App\Models\Supplier;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $outlets = Outlet::all();
        $suppliers = Supplier::all();
        // Ambil kategori yang merupakan sub-kategori (parent_id tidak null)
        $subCategories = Category::whereNotNull('parent_id')->get();

        $products = [['name' => 'Whiskas Tuna Adult 1.2kg', 'detail' => 'Makanan kucing dewasa rasa tuna.'], ['name' => 'Royal Canin Kitten 400g', 'detail' => 'Nutrisi khusus anak kucing.'], ['name' => 'Pedigree Beef Puppy 1.5kg', 'detail' => 'Makanan anjing rasa sapi.'], ['name' => 'Drontal Cat (Obat Cacing)', 'detail' => 'Obat cacing spektrum luas untuk kucing.'], ['name' => 'Kandang Besi Lipat Tingkat Size L', 'detail' => 'Kandang besi kokoh ukuran 60x40x50 cm.'], ['name' => 'Pakan Burung Gold Coin 250g', 'detail' => 'Pakan harian bernutrisi untuk burung kicau.']];

        foreach ($products as $item) {
            Product::create([
                'name' => $item['name'],
                'detail' => $item['detail'],
                'category_id' => $subCategories->random()->id,
                'outlet_id' => $outlets->random()->id,
                'supplier_id' => $suppliers->random()->id,
                'price' => rand(35000, 350000), // Harga random antara 35rb - 350rb
                'stock' => rand(10, 100),
                'image' => 'default-product.jpg',
                'status' => 'active', // Sesuaikan dengan enum di databasemu
            ]);
        }
    }
}
