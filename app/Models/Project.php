<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'project';
    protected $primaryKey = 'id_project';

    protected $fillable = [
        'nama',
        'tahun',
        'deskripsi',
        'image_url',
        'id_user',
        'id_teknologi',
        'id_kategori',
    ];

    // Relasi Belongs-To: Project ini milik 1 User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Relasi Belongs-To: Project menggunakan 1 Teknologi
    public function teknologi()
    {
        return $this->belongsTo(Teknologi::class, 'id_teknologi', 'id_teknologi');
    }

    // Relasi Belongs-To: Project termasuk dalam 1 Kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }
}