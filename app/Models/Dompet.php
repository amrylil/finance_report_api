<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dompet extends Model
{
    use HasFactory, HasUuids;  // <-- Gunakan trait

    protected $table    = 'dompet';
    protected $fillable = ['id_pengguna', 'nama', 'saldo_awal'];

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }
}
