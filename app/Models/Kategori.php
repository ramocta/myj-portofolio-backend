<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'nama_kategori',
    ];

    // Relasi One-to-Many: 1 Kategori dimiliki banyak Project
    public function projects()
    {
        return $this->hasMany(Project::class, 'id_kategori', 'id_kategori');
    }
}