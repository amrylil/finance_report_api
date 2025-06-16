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
        Schema::create('dompet', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // Ganti foreignId() dengan foreignUuid()
            $table->foreignUuid('id_pengguna')->constrained('users')->onDelete('cascade');
            $table->string('nama');
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dompet');
    }
};
