<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $admin->assignRole('Admin');

        $ferdy = User::create([
            'name' => 'Farel Ferdyawan',
            'email' => 'ferdyganteng@gmail.com',
            'password' => Hash::make('password'),
        ]);
        
        $ferdy->assignRole('Admin');

        $user = User::create([
            'name' => 'Audrel Qiano M.H.',
            'email' => 'audrel@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $user->assignRole('User');
    }
}
