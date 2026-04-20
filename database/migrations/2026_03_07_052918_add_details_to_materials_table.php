<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('materials', function (Blueprint $table) {
            // Tambahkan kolom yang dibutuhkan oleh Controller kamu
            if (!Schema::hasColumn('materials', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('materials', 'category')) {
                $table->string('category')->nullable();
            }
        });
    }

    public function down(): void {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['description', 'category']);
        });
    }
};