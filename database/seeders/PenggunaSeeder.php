<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'ulilamry@gmail.com'],
            [
                'name'     => 'Ulil Amry',
                'password' => Hash::make('password123'),  // Ganti dengan password yang aman
            ]
        );
    }
}
