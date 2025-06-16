<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PenggunaSeeder::class,  // Harus dijalankan pertama
            KategoriSeeder::class,  // Butuh pengguna
            DompetSeeder::class,  // Butuh pengguna
            TransaksiSeeder::class,  // Butuh pengguna, kategori, dan dompet
        ]);
    }
}
