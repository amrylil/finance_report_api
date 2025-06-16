<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function kategoris()
    {
        // hasMany(NamaModelRelasi, 'foreign_key_di_tabel_lain', 'local_key_di_tabel_ini')
        return $this->hasMany(Kategori::class, 'id_pengguna', 'id');
    }

    /**
     * Mendefinisikan relasi "satu User memiliki banyak Dompet".
     */
    public function dompets()
    {
        return $this->hasMany(Dompet::class, 'id_pengguna', 'id');
    }

    /**
     * Mendefinisikan relasi "satu User memiliki banyak Transaksi".
     */
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_pengguna', 'id');
    }
}
