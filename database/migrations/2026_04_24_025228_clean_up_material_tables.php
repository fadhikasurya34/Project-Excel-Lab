<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration (Menghapus kolom mubazir).
     */
    public function up(): void
    {
        // 1. Membersihkan tabel materials
        Schema::table('materials', function (Blueprint $table) {
            if (Schema::hasColumn('materials', 'background_image')) {
                $table->dropColumn('background_image');
            }
            if (Schema::hasColumn('materials', 'material_type')) {
                $table->dropColumn('material_type');
            }
        });

        // 2. Membersihkan tabel material_activities
        Schema::table('material_activities', function (Blueprint $table) {
            if (Schema::hasColumn('material_activities', 'x_percent')) {
                $table->dropColumn('x_percent');
            }
            if (Schema::hasColumn('material_activities', 'y_percent')) {
                $table->dropColumn('y_percent');
            }
            if (Schema::hasColumn('material_activities', 'explanation_content')) {
                $table->dropColumn('explanation_content');
            }
            // Menghapus foreign key constraint dulu (jika ada), baru hapus kolom user_id
            if (Schema::hasColumn('material_activities', 'user_id')) {
                            // Putus ikatan relasinya terlebih dahulu
                            $table->dropForeign(['user_id']); 
                            
                            // Baru hapus kolomnya
                            $table->dropColumn('user_id');
                        }
        });
    }

    /**
     * Kembalikan migration (Jika terjadi rollback).
     */
    public function down(): void
    {
        // 1. Kembalikan kolom di materials
        Schema::table('materials', function (Blueprint $table) {
            $table->string('background_image')->nullable();
            $table->string('material_type')->nullable();
        });

        // 2. Kembalikan kolom di material_activities
        Schema::table('material_activities', function (Blueprint $table) {
            $table->decimal('x_percent', 5, 2)->nullable();
            $table->decimal('y_percent', 5, 2)->nullable();
            $table->text('explanation_content')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
        });
    }
};