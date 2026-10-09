<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project', function (Blueprint $table) {
            $table->id('id_project');
            $table->string('nama');
            $table->year('tahun');
            $table->text('deskripsi')->nullable();
            $table->string('image_url')->nullable();
            
            // Foreign Keys
            $table->foreignId('id_user')
                  ->constrained('user', 'id_user')
                  ->onDelete('cascade');

            $table->foreignId('id_teknologi')
                  ->constrained('teknologi', 'id_teknologi')
                  ->onDelete('cascade');

            $table->foreignId('id_kategori')
                  ->constrained('kategori', 'id_kategori')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project');
    }
};