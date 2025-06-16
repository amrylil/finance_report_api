<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $pengguna = User::where('email', 'ulilamry@gmail.com')->first();
        if (!$pengguna) {
            $this->command->info('Pengguna tidak ditemukan, TransaksiSeeder dilewati.');
            return;
        }

        // Ambil data master kategori dan dompet milik pengguna
        $kategoris = $pengguna->kategoris()->get()->keyBy('nama');
        $dompets   = $pengguna->dompets()->get()->keyBy('nama');

        if ($kategoris->isEmpty() || $dompets->isEmpty()) {
            $this->command->info('Kategori atau Dompet kosong, pastikan seeder lain sudah berjalan.');
            return;
        }

        // Hapus transaksi lama
        $pengguna->transaksis()->delete();

        // 1. Buat Transaksi Gaji (Pemasukan)
        Transaksi::create([
            'id_pengguna' => $pengguna->id,
            'id_kategori' => $kategoris['Gaji']->id,
            'id_dompet'   => $dompets['Rekening BCA']->id,
            'deskripsi'   => 'Gaji bulanan',
            'jumlah'      => 7500000,
            'tipe'        => 'pemasukan',
            'tanggal'     => Carbon::now()->startOfMonth(),
        ]);

        // 2. Buat Transaksi Pengeluaran Acak untuk 30 hari terakhir
        for ($i = 0; $i < 50; $i++) {
            $tipeKategori = $kategoris->where('tipe', 'pengeluaran')->random();
            $dompet       = $dompets->random();

            Transaksi::create([
                'id_pengguna' => $pengguna->id,
                'id_kategori' => $tipeKategori->id,
                'id_dompet'   => $dompet->id,
                'deskripsi'   => 'Pengeluaran untuk ' . $tipeKategori->nama,
                'jumlah'      => rand(15000, 200000),
                'tipe'        => 'pengeluaran',
                'tanggal'     => Carbon::now()->subDays(rand(0, 30)),
            ]);
        }
    }
}
