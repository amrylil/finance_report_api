<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    //
    use HasFactory, HasUuids;  // <-- Gunakan trait

    protected $table = 'transaksi';

    protected $fillable = [
        'id_pengguna', 'id_kategori', 'id_dompet',
        'deskripsi', 'jumlah', 'tipe', 'tanggal'
    ];

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function dompet()
    {
        return $this->belongsTo(Dompet::class, 'id_dompet');
    }
}
