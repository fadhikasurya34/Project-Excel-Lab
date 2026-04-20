<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('mission_hotspots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('step_id')->constrained('mission_steps')->onDelete('cascade');
            $table->decimal('x_percent', 5, 2);
            $table->decimal('y_percent', 5, 2);
            $table->text('content'); // Pesan instruksi per titik
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_hotspots');
    }
};
