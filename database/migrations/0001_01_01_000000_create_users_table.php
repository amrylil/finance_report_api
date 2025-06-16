<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // === Tabel 'users' (atau 'penggunas') ===
        // Nama tabel bisa Anda ganti menjadi 'penggunas' jika ingin konsisten.
        Schema::create('users', function (Blueprint $table) {
            // PERUBAHAN 1: Mengganti id() dengan uuid()
            $table->uuid('id')->primary();

            // Kolom lainnya tetap sama, bisa disesuaikan namanya ke Bahasa Indonesia.
            $table->string('name');  // -> bisa jadi 'nama'
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');  // -> bisa jadi 'kata_sandi'
            $table->rememberToken();
            $table->timestamps();
        });

        // === Tabel 'password_reset_tokens' ===
        // TIDAK ADA PERUBAHAN DI SINI.
        // Tabel ini menghubungkan token reset ke 'email', bukan ke 'user_id'.
        // Jadi, tidak terpengaruh oleh perubahan tipe ID di tabel users.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // === Tabel 'sessions' ===
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();

            // PERUBAHAN 2: Mengganti foreignId() dengan foreignUuid()
            // Ini PENTING agar tipe data kolom foreign key (UUID) cocok
            // dengan tipe data primary key di tabel 'users' (UUID).
            $table->foreignUuid('user_id')->nullable()->index();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
