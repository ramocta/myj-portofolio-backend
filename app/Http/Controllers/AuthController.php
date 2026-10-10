<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Jurusan;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // ==========================================
    // 1. MENGAMBIL DATA JURUSAN & PRODI UNTUK FORM APLIKASI
    // ==========================================
    public function getJurusan()
    {
        $jurusan = Jurusan::all();
        return response()->json([
            'status' => 'success',
            'data'   => $jurusan
        ], 200);
    }

    public function getProdi()
    {
        $prodi = Prodi::all();
        return response()->json(['status' => 'success', 'data' => $prodi], 200);
    }

    // ==========================================
    // 2. PROSES REGISTRASI API
    // ==========================================
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'       => 'required|string|max:255',
            'nim'        => ['required', 'string', 'regex:/^[eE]412[0-9]{5}$/', 'unique:user,nim'],
            'angkatan'   => 'required|digits:4|integer',
            'username'   => 'required|string|unique:user,username',
            'password'   => 'required|string|min:6',
            'id_jurusan' => 'required|exists:jurusan,id_jurusan',
            'id_prodi'   => 'required|exists:prodi,id_prodi',
        ], [
            'nim.regex'         => 'Format NIM harus diawali e412 diikuti 5 angka (Contoh: e41212345).',
            'nim.unique'        => 'NIM sudah terdaftar.',
            'username.unique'   => 'Username sudah digunakan.',
            'id_prodi.exists'   => 'Program Studi yang dipilih tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'nama'       => $request->nama,
            'nim'        => strtolower($request->nim),
            'angkatan'   => $request->angkatan,
            'username'   => $request->username,
            'password'   => Hash::make($request->password),
            'id_jurusan' => $request->id_jurusan,
            'id_prodi'   => $request->id_prodi, // <-- Insert Prodi
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Registrasi berhasil',
            'data'    => $user
        ], 201);
    }

    // ==========================================
    // 3. PROSES LOGIN API
    // ==========================================
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cari user berdasarkan username
        $user = User::where('username', $request->username)->first();

        // Cek apakah user ada dan password cocok
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Username atau Password salah'
            ], 401);
        }

        // Buat token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'       => 'success',
            'message'      => 'Login berhasil',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'data'         => $user
        ], 200);
    }

    // ==========================================
    // 4. PROSES LOGOUT API
    // ==========================================
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan untuk request ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil logout dan token dihapus'
        ], 200);
    }
}