<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('nama');
            $table->string('nim')->unique();
            $table->year('angkatan');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('image_profil')->nullable();
            $table->text('deskripsi')->nullable();
            
            // Tambahan: Foreign Key Prodi
            $table->foreignId('id_prodi')
                  ->constrained('prodi', 'id_prodi')
                  ->onDelete('cascade');

                  
            // Foreign Key Jurusan
            $table->foreignId('id_jurusan')
                  ->constrained('jurusan', 'id_jurusan')
                  ->onDelete('cascade');


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
