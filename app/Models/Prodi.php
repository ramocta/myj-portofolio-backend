<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;

    protected $table = 'prodi';
    protected $primaryKey = 'id_prodi';

    protected $fillable = [
        'nama_prodi',
        // 'id_jurusan', // Buka komentar ini jika prodi berelasi langsung dengan tabel jurusan
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_prodi', 'id_prodi');
    }
}