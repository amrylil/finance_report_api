<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dompet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DompetController extends Controller
{
    /**
     * Menampilkan semua dompet milik pengguna yang login.
     */
    public function index(Request $request)
    {
        // Mengambil dompet berdasarkan relasi dari model Pengguna
        $dompets = $request->user()->dompets()->get();

        return response()->json([
            'success' => true,
            'data'    => $dompets
        ]);
    }

    /**
     * Menyimpan dompet baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'       => 'required|string|max:255',
            'saldo_awal' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Membuat dompet baru yang id_pengguna-nya otomatis diisi
        $dompet = $request->user()->dompets()->create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Dompet berhasil ditambahkan!',
            'data'    => $dompet
        ], 201);
    }

    /**
     * Menampilkan detail satu dompet.
     */
    public function show(Request $request, Dompet $dompet)
    {
        // Pengecekan otorisasi: pastikan dompet ini milik pengguna yang login
        if ($request->user()->id !== $dompet->id_pengguna) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        return response()->json(['success' => true, 'data' => $dompet]);
    }

    /**
     * Memperbarui dompet.
     */
    public function update(Request $request, Dompet $dompet)
    {
        // Pengecekan otorisasi
        if ($request->user()->id !== $dompet->id_pengguna) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama'       => 'sometimes|required|string|max:255',
            'saldo_awal' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $dompet->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Dompet berhasil diperbarui!',
            'data'    => $dompet
        ]);
    }

    /**
     * Menghapus dompet.
     */
    public function destroy(Request $request, Dompet $dompet)
    {
        // Pengecekan otorisasi
        if ($request->user()->id !== $dompet->id_pengguna) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        // Peringatan: Jika Anda mengatur onDelete('cascade') pada migrasi transaksi,
        // maka semua transaksi yang terkait dengan dompet ini akan ikut terhapus.
        $dompet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dompet berhasil dihapus!'
        ]);
    }
}
