<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;  // <-- Import Rule untuk validasi lanjutan

class TransaksiController extends Controller
{
    /**
     * Menampilkan semua transaksi milik pengguna yang login.
     */
    public function index(Request $request)
    {
        // Menggunakan with() untuk Eager Loading agar lebih efisien.
        // Ini akan mengambil data transaksi sekaligus data kategori dan dompetnya.
        $transaksis = $request
            ->user()
            ->transaksis()
            ->with(['kategori', 'dompet'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $transaksis
        ]);
    }

    /**
     * Menyimpan transaksi baru.
     */
    public function store(Request $request)
    {
        $penggunaId = $request->user()->id;

        $validator = Validator::make($request->all(), [
            'deskripsi'   => 'required|string|max:255',
            'jumlah'      => 'required|numeric|min:0.01',
            'tipe'        => 'required|in:pemasukan,pengeluaran',
            'tanggal'     => 'required|date',
            // Validasi canggih:
            // Pastikan id_kategori ada di tabel 'kategoris' DAN id_kategori tersebut milik pengguna yang sedang login.
            'id_kategori' => [
                'required',
                Rule::exists('kategori', 'id')->where(function ($query) use ($penggunaId) {
                    return $query->where('id_pengguna', $penggunaId);
                }),
            ],
            // Lakukan hal yang sama untuk id_dompet
            'id_dompet'   => [
                'required',
                Rule::exists('dompet', 'id')->where(function ($query) use ($penggunaId) {
                    return $query->where('id_pengguna', $penggunaId);
                }),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Membuat transaksi baru yang id_pengguna-nya otomatis diisi
        $transaksi = $request->user()->transaksis()->create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil ditambahkan!',
            // Muat relasinya agar respons API berisi nama kategori dan dompet
            'data'    => $transaksi->load(['kategori', 'dompet'])
        ], 201);
    }

    /**
     * Menampilkan detail satu transaksi.
     */
    public function show(Request $request, Transaksi $transaksi)
    {
        // Pengecekan otorisasi
        if ($request->user()->id !== $transaksi->id_pengguna) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        return response()->json([
            'success' => true,
            // Muat relasinya agar respons API berisi nama kategori dan dompet
            'data'    => $transaksi->load(['kategori', 'dompet'])
        ]);
    }

    /**
     * Memperbarui transaksi.
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        // Pengecekan otorisasi
        if ($request->user()->id !== $transaksi->id_pengguna) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $penggunaId = $request->user()->id;

        // Validasi sama seperti store, tapi dengan 'sometimes'
        $validator = Validator::make($request->all(), [
            'deskripsi'   => 'sometimes|required|string|max:255',
            'jumlah'      => 'sometimes|required|numeric|min:0.01',
            'tipe'        => 'sometimes|required|in:pemasukan,pengeluaran',
            'tanggal'     => 'sometimes|required|date',
            'id_kategori' => [
                'sometimes',
                'required',
                Rule::exists('kategori', 'id')->where('id_pengguna', $penggunaId),
            ],
            'id_dompet'   => [
                'sometimes',
                'required',
                Rule::exists('dompet', 'id')->where('id_pengguna', $penggunaId),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $transaksi->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil diperbarui!',
            'data'    => $transaksi->load(['kategori', 'dompet'])
        ]);
    }

    /**
     * Menghapus transaksi.
     */
    public function destroy(Request $request, Transaksi $transaksi)
    {
        // Pengecekan otorisasi
        if ($request->user()->id !== $transaksi->id_pengguna) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $transaksi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus!'
        ]);
    }
}
