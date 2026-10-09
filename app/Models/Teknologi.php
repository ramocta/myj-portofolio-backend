<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teknologi extends Model
{
    use HasFactory;

    protected $table = 'teknologi';
    protected $primaryKey = 'id_teknologi';

    protected $fillable = [
        'nama_teknologi',
    ];

    // Relasi One-to-Many: 1 Teknologi digunakan oleh banyak Project
    public function projects()
    {
        return $this->hasMany(Project::class, 'id_teknologi', 'id_teknologi');
    }
}