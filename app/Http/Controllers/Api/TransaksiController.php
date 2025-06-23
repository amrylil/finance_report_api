<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dompet;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'id_kategori' => [
                'required',
                Rule::exists('kategori', 'id')->where('id_pengguna', $penggunaId),
            ],
            'id_dompet'   => [
                'required',
                Rule::exists('dompet', 'id')->where('id_pengguna', $penggunaId),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Mulai transaksi database
        $transaksi = DB::transaction(function () use ($request, $validator) {
            $data = $validator->validated();

            // Buat transaksi
            $transaksi = $request->user()->transaksis()->create($data);

            // Update saldo_awal dompet
            $dompet = Dompet::find($data['id_dompet']);
            if ($data['tipe'] === 'pemasukan') {
                $dompet->saldo_awal += $data['jumlah'];
            } else {  // pengeluaran
                $dompet->saldo_awal -= $data['jumlah'];
            }
            $dompet->save();

            return $transaksi;
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil ditambahkan!',
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
        if ($request->user()->id !== $transaksi->id_pengguna) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $penggunaId = $request->user()->id;

        $validator = Validator::make($request->all(), [
            'deskripsi'   => 'sometimes|required|string|max:255',
            'jumlah'      => 'sometimes|required|numeric|min:0.01',
            'tipe'        => 'sometimes|required|in:pemasukan,pengeluaran',
            'tanggal'     => 'sometimes|required|date',
            'id_kategori' => ['sometimes', 'required', Rule::exists('kategori', 'id')->where('id_pengguna', $penggunaId)],
            'id_dompet'   => ['sometimes', 'required', Rule::exists('dompet', 'id')->where('id_pengguna', $penggunaId)],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Mulai transaksi database
        DB::transaction(function () use ($request, $validator, $transaksi) {
            // 1. Kembalikan saldo dompet lama
            $dompetLama = $transaksi->dompet;
            if ($transaksi->tipe === 'pemasukan') {
                $dompetLama->saldo_awal -= $transaksi->jumlah;
            } else {
                $dompetLama->saldo_awal += $transaksi->jumlah;
            }
            $dompetLama->save();

            // 2. Lakukan update pada transaksi
            $dataUpdate = $validator->validated();
            $transaksi->update($dataUpdate);

            // 3. Sesuaikan saldo_awal dompet baru (bisa jadi dompet yang sama atau berbeda)
            $dompetBaru = Dompet::find($transaksi->id_dompet);
            if ($transaksi->tipe === 'pemasukan') {
                $dompetBaru->saldo_awal += $transaksi->jumlah;
            } else {
                $dompetBaru->saldo_awal -= $transaksi->jumlah;
            }
            $dompetBaru->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil diperbarui!',
            'data'    => $transaksi->fresh()->load(['kategori', 'dompet'])  // Ambil data terbaru
        ]);
    }

    /**
     * Menghapus transaksi.
     */
    public function destroy(Request $request, Transaksi $transaksi)
    {
        if ($request->user()->id !== $transaksi->id_pengguna) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        // Mulai transaksi database
        DB::transaction(function () use ($transaksi) {
            // Kembalikan saldo dompet sebelum menghapus transaksi
            $dompet = $transaksi->dompet;
            if ($transaksi->tipe === 'pemasukan') {
                $dompet->saldo_awal -= $transaksi->jumlah;
            } else {  // pengeluaran
                $dompet->saldo_awal += $transaksi->jumlah;
            }
            $dompet->save();

            // Hapus transaksi
            $transaksi->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus!'
        ]);
    }
}
