<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teknologi', function (Blueprint $table) {
            $table->id('id_teknologi');
            $table->string('nama_teknologi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teknologi');
    }
};