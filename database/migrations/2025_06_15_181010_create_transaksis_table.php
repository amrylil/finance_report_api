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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('id_pengguna')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('id_kategori')->constrained('kategori')->onDelete('cascade');
            $table->foreignUuid('id_dompet')->constrained('dompet')->onDelete('cascade');
            $table->string('deskripsi');
            $table->decimal('jumlah', 15, 2);
            $table->enum('tipe', ['pemasukan', 'pengeluaran']);
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
