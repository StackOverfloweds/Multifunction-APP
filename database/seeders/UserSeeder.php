<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Super Admin
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'email' => 'superadmin@example.com',
                'password' => Hash::make('pengenTau'),
                'role' => 'super_admin',
            ]
        );

        // 2. Akun Admin
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'email' => 'admin@example.com',
                'password' => Hash::make('pengenTau'),
                'role' => 'admin',
            ]
        );

        // 3. Akun User Biasa
        User::updateOrCreate(
            ['username' => 'user'],
            [
                'email' => 'user@example.com',
                'password' => Hash::make('pengenTau'),
                'role' => 'user',
            ]
        );
    }
}