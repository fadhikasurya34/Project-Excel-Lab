<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('hotspots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_activity_id')->constrained()->onDelete('cascade');
            $table->decimal('x_percent', 5, 2);
            $table->decimal('y_percent', 5, 2);
            $table->text('content'); // Bisa diisi teks atau link iframe video youtube
            $table->string('type')->default('text'); // 'text' atau 'video'
            $table->timestamps();
        });
    }
        /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotspots');
    }
};
