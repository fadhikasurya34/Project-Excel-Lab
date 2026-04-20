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
        Schema::create('material_activities', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel materials 
            $table->foreignId('material_id')->constrained()->onDelete('cascade'); 
            
            // Koordinat dalam persen agar responsif di semua layar 
            $table->decimal('x_percent', 5, 2); 
            $table->decimal('y_percent', 5, 2); 
            
            // Konten penjelasan atau instruksi interaktif 
            $table->string('instruction')->nullable(); 
            $table->text('explanation_content'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_activities');
    }
};