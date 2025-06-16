<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;  // Cukup gunakan 'User'
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;  // <-- 1. Import class Throwable

class AuthController extends Controller
{
  /**
   * Handle user registration.
   */
  public function register(Request $request)
  {
    // <-- 2. Tambahkan blok try
    try {
      $validator = Validator::make($request->all(), [
        'name'     => 'required|string|max:255',
        'email'    => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
      ]);

      // Buat token untuk pengguna baru
      $token = $user->createToken('auth_token')->plainTextToken;

      return response()->json([
        'message'      => 'Registrasi berhasil',
        'data'         => $user,
        'access_token' => $token,
        'token_type'   => 'Bearer',
      ], 201);
    } catch (Throwable $th) {
      // <-- 3. Tangkap semua jenis error
      return response()->json([
        'success' => false,
        'message' => 'Registrasi gagal, terjadi kesalahan pada server.',
        'error'   => $th->getMessage()
      ], 500);
    }
  }

  /**
   * Handle user login.
   */
  public function login(Request $request)
  {
    // <-- 4. Tambahkan blok try
    try {
      $validator = Validator::make($request->all(), [
        'email'    => 'required|string|email',
        'password' => 'required|string',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'Email atau kata sandi salah'], 401);
      }

      $user = User::where('email', $request['email'])->firstOrFail();

      $token = $user->createToken('auth_token')->plainTextToken;

      return response()->json([
        'message'      => 'Login berhasil',
        'data'         => $user,
        'access_token' => $token,
        'token_type'   => 'Bearer',
      ]);
    } catch (Throwable $th) {
      // <-- 5. Tangkap semua jenis error
      return response()->json([
        'success' => false,
        'message' => 'Login gagal, terjadi kesalahan pada server.',
        'error'   => $th->getMessage()  // Ini akan memberi pesan error yg sama spt di laravel.log
      ], 500);
    }
  }

  /**
   * Handle user logout.
   */
  public function logout(Request $request)
  {
    // Hapus token yang sedang digunakan untuk request ini
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logout berhasil']);
  }
}
