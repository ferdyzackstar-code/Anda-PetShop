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
            'image' => 'default-user.jpg',
            'bio' => 'Admin of Anda Petshop.'
        ]);

        $admin->assignRole('Admin');

        $ferdy = User::create([
            'name' => 'Farel Ferdyawan',
            'email' => 'ferdyganteng@gmail.com',
            'password' => Hash::make('password'),
            'image' => 'default-user.jpg',
            'bio' => 'Lead Developer of Anda Petshop & Ferdy Blog.'
        ]);
        
        $ferdy->assignRole('Admin');

        $user = User::create([
            'name' => 'Audrel Qiano M.H.',
            'email' => 'audrel@gmail.com',
            'password' => Hash::make('password'),
            'image' => 'default-user.jpg',
            'bio' => 'Employee Of Anda Petshop.'
        ]);

        $user->assignRole('User');
    }
}
