<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama',
        'nim',
        'angkatan',
        'username',
        'password',
        'image_profil',
        'deskripsi',
        'id_jurusan',
        'id_prodi', // <-- Tambahan
    ];

    // Sembunyikan password saat data diambil (misal untuk API)
    protected $hidden = [
        'password',
    ];

    // Relasi Belongs-To: User memiliki 1 Jurusan
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi', 'id_prodi');
    }

    // Relasi One-to-Many: 1 User memiliki banyak Project
    public function projects()
    {
        return $this->hasMany(Project::class, 'id_user', 'id_user');
    }
}
