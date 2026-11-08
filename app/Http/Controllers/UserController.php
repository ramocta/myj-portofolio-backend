<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ==========================================
    // GET ALL USERS (Mahasiswa)
    // ==========================================
    public function index(Request $request)
    {
        // Ambil ID user yang sedang login saat ini dari token Sanctum
        $loggedInUserId = $request->user()->id_user;

        // Mengambil semua user beserta jurusan, TETAPI KECUALIKAN id_user yang sedang login
        $users = User::with('jurusan')
            ->where('id_user', '!=', $loggedInUserId)
            ->get();
        
        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mengambil data mahasiswa',
            'data'    => $users
        ], 200);
    }
    
    // Nanti Anda bisa menambahkan method lain di sini jika butuh, 
    // seperti show() untuk get detail 1 user, dll.
}