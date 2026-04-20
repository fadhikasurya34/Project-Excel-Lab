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
        Schema::table('hotspots', function (Blueprint $table) {
            // Menambahkan kolom order untuk menyimpan urutan titik hotspot
            $table->integer('order')->default(0)->after('type'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotspots', function (Blueprint $table) {
            // Menghapus kolom order jika migrasi di-rollback
            $table->dropColumn('order');
        });
    }
};