<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        $outlets = \App\Models\Outlet::all();
        $user = \App\Models\User::first();

        if ($outlets->isEmpty()) {
            $this->command->info('Buat outlet dulu bos, baru bisa bikin transaksi!');
            return; 
        }

        foreach ($outlets as $outlet) {
            // Kita buat 10 transaksi random per outlet
            for ($i = 1; $i <= 10; $i++) {
                \App\Models\Transaction::create([
                    'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4)),
                    'outlet_id' => $outlet->id,
                    'user_id' => $user->id,
                    'total_price' => rand(100000, 500000), // Angka random 100rb - 500rb
                    'paid_amount' => 500000,
                    'change_amount' => 0,
                    'created_at' => now()->subDays(rand(0, 7)), // Biar ada data seminggu terakhir
                ]);
            }
        }
        $this->command->info('Seeding Transaksi Berhasil!');
    }
}
