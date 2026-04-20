<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('missions', function (Blueprint $table) {
            $table->string('mission_image')->nullable()->after('level_id'); // Gambar khusus skenario
            $table->float('target_x')->nullable(); // Koordinat X (%)
            $table->float('target_y')->nullable(); // Koordinat Y (%)
            $table->text('distractors')->nullable(); // Potongan rumus jebakan (JSON/String)
        });
    }

    public function down(): void {
        Schema::table('missions', function (Blueprint $table) {
            $table->dropColumn(['mission_image', 'target_x', 'target_y', 'distractors']);
        });
    }
};