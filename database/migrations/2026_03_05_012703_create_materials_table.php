<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            // Menghubungkan materi ke level tertentu agar urutannya sistematis [cite: 535, 644]
            $table->foreignId('level_id')->nullable()->constrained()->onDelete('cascade'); 
            
            // Judul Modul Materi 
            $table->string('title'); 
            
            // Path gambar antarmuka Excel asli yang diunggah Admin/Guru [cite: 589, 782]
            $table->string('background_image'); 
            
            // Menentukan tipe materi (misal: hotspot atau video) 
            $table->string('material_type')->default('hotspot'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};