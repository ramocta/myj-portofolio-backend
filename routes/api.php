<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;

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
            'data'   => $request->user()->load('jurusan')
        ], 200);
    });

    // Rute Update Profil (Gambar & Deskripsi) dan Hapus Foto Profil
    Route::post('/profile/update', [ProfileController::class, 'updateProfile']); 
    Route::delete('/profile/image', [ProfileController::class, 'deleteProfileImage']);

    // Rute CRUD Project (Hanya bisa diakses jika sudah login)
    Route::post('/projects', [ProjectController::class, 'store']); 
    Route::post('/projects/{id}', [ProjectController::class, 'update']); // Update (Pakai POST agar upload file mudah)
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']); // Delete
});