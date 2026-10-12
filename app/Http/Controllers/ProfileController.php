<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    // ==========================================
    // 1. UPDATE PROFIL (Gambar & Deskripsi)
    // ==========================================
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'deskripsi'    => 'nullable|string',
            'image_profil' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        // Jika user upload gambar baru
        if ($request->hasFile('image_profil')) {
            // Hapus gambar lama dari storage jika ada
            if ($user->image_profil && Storage::disk('public')->exists($user->image_profil)) {
                Storage::disk('public')->delete($user->image_profil);
            }

            // Simpan gambar baru
            $imagePath = $request->file('image_profil')->store('profile_images', 'public');
            $user->image_profil = $imagePath;
        }

        // Jika user mengupdate deskripsi
        if ($request->has('deskripsi')) {
            $user->deskripsi = $request->deskripsi;
        }

        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil berhasil diperbarui',
            'data'    => $user
        ], 200);
    }

    // ==========================================
    // 2. HAPUS FOTO PROFIL SAJA
    // ==========================================
    public function deleteProfileImage(Request $request)
    {
        $user = $request->user();

        // Cek apakah user punya foto profil
        if ($user->image_profil && Storage::disk('public')->exists($user->image_profil)) {
            // Hapus file fisik dari folder storage
            Storage::disk('public')->delete($user->image_profil);
            
            // Set kolom image_profil di database menjadi null
            $user->image_profil = null;
            $user->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Foto profil berhasil dihapus'
            ], 200);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'User tidak memiliki foto profil untuk dihapus'
        ], 404);
    }
}
