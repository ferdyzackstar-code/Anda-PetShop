<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            // --- Seeder ACL & User (Bawaan Kamu) ---
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,

            // --- Seeder Master Data Petshop ---
            OutletSeeder::class,
            CategorySeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class, // Product di bawah karena butuh ID dari atas
        ]);
    }
}
