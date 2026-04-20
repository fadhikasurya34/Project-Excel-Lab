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
        Schema::create('scores_and_rankings', function (Blueprint $table) {
            $table->id();
            // WAJIB ADA BARIS INI:
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->integer('total_xp')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores_and_rankings');
    }
};
