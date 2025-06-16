<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// Import semua Controller yang akan kita gunakan
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DompetController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\TransaksiController;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

// ===================================================================
// RUTE TERPROTEKSI (Wajib Login & Mengirimkan Token Sanctum)
// ===================================================================
Route::middleware('auth:sanctum')->group(function () {
  Route::get('/user', function (Request $request) {
    return $request->user();
  });

  // Rute untuk logout
  Route::post('/logout', [AuthController::class, 'logout']);

  Route::apiResource('kategori', KategoriController::class);

  // Rute CRUD (Resource) untuk Dompet
  Route::apiResource('dompet', DompetController::class);

  // Rute CRUD (Resource) untuk Transaksi
  Route::apiResource('transaksi', TransaksiController::class);
});
