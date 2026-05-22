<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat akun admin untuk login Barokah Toserba
        User::updateOrCreate(
            ['username' => 'admin'], // Kalau username admin udah ada, gak bakal bikin duplikat
            [
                'name' => 'Admin Barokah',
                'password' => Hash::make('password'), // Password otomatis di-enkripsi bcrypt
            ]
        );
    }
}