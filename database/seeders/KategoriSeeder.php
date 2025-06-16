<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Pengguna;
use App\Models\User;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $pengguna = User::where('email', 'ulilamry@gmail.com')->first();
        if (!$pengguna) {
            $this->command->info('Pengguna tidak ditemukan, KategoriSeeder dilewati.');
            return;
        }

        // Hapus kategori lama untuk menghindari duplikasi
        $pengguna->kategoris()->delete();

        $data = [
            'pemasukan'   => ['Gaji', 'Bonus', 'Freelance', 'Hadiah'],
            'pengeluaran' => ['Makan & Minum', 'Transportasi', 'Tagihan', 'Belanja', 'Kesehatan', 'Hiburan'],
        ];

        foreach ($data as $tipe => $kategoris) {
            foreach ($kategoris as $nama) {
                Kategori::create([
                    'id_pengguna' => $pengguna->id,
                    'nama'        => $nama,
                    'tipe'        => $tipe,
                ]);
            }
        }
    }
}
