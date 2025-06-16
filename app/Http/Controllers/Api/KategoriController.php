<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    /**
     * Menampilkan semua kategori MILIK PENGGUNA yang sedang login.
     */
    public function index(Request $request)
    {
        $kategoris = $request->user()->kategoris()->get();  // Mengambil relasi 'kategoris' dari model Pengguna

        return response()->json([
            'success' => true,
            'data'    => $kategoris
        ]);
    }

    /**
     * Menyimpan kategori baru MILIK PENGGUNA yang sedang login.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:pemasukan,pengeluaran',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Membuat kategori baru yang langsung terhubung dengan pengguna yang login
        $kategori = $request->user()->kategoris()->create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan!',
            'data'    => $kategori
        ], 201);
    }

    /**
     * Menampilkan detail satu kategori.
     */
    public function show(Request $request, Kategori $kategori)
    {
        // Pastikan kategori ini milik pengguna yang sedang login
        if ($request->user()->id !== $kategori->id_pengguna) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        return response()->json(['success' => true, 'data' => $kategori]);
    }

    /**
     * Memperbarui kategori.
     */
    public function update(Request $request, Kategori $kategori)
    {
        // Pastikan kategori ini milik pengguna yang sedang login
        if ($request->user()->id !== $kategori->id_pengguna) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|required|string|max:255',
            'tipe' => 'sometimes|required|in:pemasukan,pengeluaran',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $kategori->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui!',
            'data'    => $kategori
        ]);
    }

    /**
     * Menghapus kategori.
     */
    public function destroy(Request $request, Kategori $kategori)
    {
        // Pastikan kategori ini milik pengguna yang sedang login
        if ($request->user()->id !== $kategori->id_pengguna) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $kategori->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus!'
        ]);
    }
}
