<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// ENDPOINT PUBLIK (Tidak Perlu Token Login)
// ==========================================

// 1. Get Data Jurusan & Prodi (Untuk dropdown di form register aplikasi)
Route::get('/jurusan', [AuthController::class, 'getJurusan']);
Route::get('/prodi', [AuthController::class, 'getProdi']);

// 2. Register
Route::post('/register', [AuthController::class, 'register']);

// 3. Login
Route::post('/login', [AuthController::class, 'login']);


// ==========================================
// ENDPOINT TERPROTEKSI (Wajib pakai Bearer Token)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    
    // 4. Logout (Menghapus token saat ini)
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // 5. Get Profile User yang sedang login
    Route::get('/profile', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data'   => $request->user()->load('jurusan') // load relasi jurusan
        ], 200);
    });

    // Nanti Anda bisa menambahkan endpoint untuk tabel Project, Teknologi, Kategori di sini
    // Route::get('/projects', [ProjectController::class, 'index']);
});