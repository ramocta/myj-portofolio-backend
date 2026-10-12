<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    // ==========================================
    // 1. GET ALL PROJECTS (Bisa dilihat siapa saja)
    // ==========================================
    public function index()
    {
        // Mengambil semua project beserta data user, teknologi, dan kategorinya
        $projects = Project::with(['user', 'teknologi', 'kategori'])->get();
        
        return response()->json([
            'status' => 'success',
            'data'   => $projects
        ], 200);
    }

    // ==========================================
    // 2. GET PROJECT BERDASARKAN ID (Detail)
    // ==========================================
    public function show($id)
    {
        $project = Project::with(['user', 'teknologi', 'kategori'])->find($id);

        if (!$project) {
            return response()->json(['status' => 'error', 'message' => 'Project tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $project
        ], 200);
    }

    // ==========================================
    // 3. CREATE PROJECT BARU
    // ==========================================
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'         => 'required|string|max:255',
            'tahun'        => 'required|digits:4|integer',
            'deskripsi'    => 'nullable|string',
            'image_url'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'id_teknologi' => 'required|exists:teknologi,id_teknologi',
            'id_kategori'  => 'required|exists:kategori,id_kategori',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image_url')) {
            // Simpan gambar ke folder storage/app/public/project_images
            $imagePath = $request->file('image_url')->store('project_images', 'public');
        }

        // Simpan data project. Ambil id_user otomatis dari user yang sedang login
        $project = Project::create([
            'nama'         => $request->nama,
            'tahun'        => $request->tahun,
            'deskripsi'    => $request->deskripsi,
            'image_url'    => $imagePath,
            'id_user'      => $request->user()->id_user, // FK dari token login
            'id_teknologi' => $request->id_teknologi,
            'id_kategori'  => $request->id_kategori,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Project berhasil ditambahkan',
            'data'    => $project
        ], 201);
    }

    // ==========================================
    // 4. UPDATE PROJECT
    // ==========================================
    // Catatan: Kita menggunakan method POST (bukan PUT) karena form-data (file upload) 
    // lebih mudah ditangani dengan POST di PHP/Laravel.
    public function update(Request $request, $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json(['status' => 'error', 'message' => 'Project tidak ditemukan'], 404);
        }

        // Otorisasi: Pastikan user yang login adalah pemilik project ini
        if ($project->id_user !== $request->user()->id_user) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak berhak mengedit project ini'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama'         => 'sometimes|required|string|max:255',
            'tahun'        => 'sometimes|required|digits:4|integer',
            'deskripsi'    => 'nullable|string',
            'image_url'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'id_teknologi' => 'sometimes|required|exists:teknologi,id_teknologi',
            'id_kategori'  => 'sometimes|required|exists:kategori,id_kategori',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        // Cek jika ada upload gambar baru
        if ($request->hasFile('image_url')) {
            // Hapus gambar lama jika ada
            if ($project->image_url && Storage::disk('public')->exists($project->image_url)) {
                Storage::disk('public')->delete($project->image_url);
            }
            // Simpan yang baru
            $project->image_url = $request->file('image_url')->store('project_images', 'public');
        }

        // Update field lainnya jika ada di request
        $project->nama = $request->nama ?? $project->nama;
        $project->tahun = $request->tahun ?? $project->tahun;
        $project->deskripsi = $request->deskripsi ?? $project->deskripsi;
        $project->id_teknologi = $request->id_teknologi ?? $project->id_teknologi;
        $project->id_kategori = $request->id_kategori ?? $project->id_kategori;

        $project->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Project berhasil diupdate',
            'data'    => $project
        ], 200);
    }

    // ==========================================
    // 5. DELETE PROJECT
    // ==========================================
    public function destroy(Request $request, $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json(['status' => 'error', 'message' => 'Project tidak ditemukan'], 404);
        }

        // Otorisasi: Pastikan user yang login adalah pemilik project ini
        if ($project->id_user !== $request->user()->id_user) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak berhak menghapus project ini'], 403);
        }

        // Hapus gambar fisik dari storage
        if ($project->image_url && Storage::disk('public')->exists($project->image_url)) {
            Storage::disk('public')->delete($project->image_url);
        }

        // Hapus data dari database
        $project->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Project berhasil dihapus'
        ], 200);
    }
}