<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserLogin;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        UserLogin::create([
            'id_user' => 'admin001',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'level_user' => 'admin',
            'id_ptk' => null,
            'status_aktif' => 1
        ]);

        // Create guru user (you can add a real PTK ID if available)
        UserLogin::create([
            'id_user' => 'guru001',
            'username' => 'guru',
            'password' => Hash::make('guru123'),
            'level_user' => 'guru',
            'id_ptk' => null, // You can set this to a real PTK ID from the database
            'status_aktif' => 1
        ]);
    }
}