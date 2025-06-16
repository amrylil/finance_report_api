<?php

namespace Database\Seeders;

use App\Models\Dompet;
use App\Models\Pengguna;
use App\Models\User;
use Illuminate\Database\Seeder;

class DompetSeeder extends Seeder
{
    public function run(): void
    {
        $pengguna = User::where('email', 'ulilamry@gmail.com')->first();
        if (!$pengguna) {
            $this->command->info('Pengguna tidak ditemukan, DompetSeeder dilewati.');
            return;
        }

        // Hapus dompet lama untuk menghindari duplikasi
        $pengguna->dompets()->delete();

        $dompets = [
            ['nama' => 'Dompet Tunai', 'saldo_awal' => 500000],
            ['nama' => 'Rekening BCA', 'saldo_awal' => 5000000],
            ['nama' => 'GoPay', 'saldo_awal' => 250000],
        ];

        foreach ($dompets as $dompet) {
            Dompet::create([
                'id_pengguna' => $pengguna->id,
                'nama'        => $dompet['nama'],
                'saldo_awal'  => $dompet['saldo_awal'],
            ]);
        }
    }
}
