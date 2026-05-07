<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Tambahkan ini agar bisa query data

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom baru TANPA menghapus yang lama dulu
        Schema::table('materials', function (Blueprint $table) {
            if (!Schema::hasColumn('materials', 'category_id')) {
                $table->foreignId('category_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('material_categories')
                      ->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('materials', 'material_type')) {
                $table->string('material_type')->default('teori')->after('description');
            }
        });

        // 2. LOGIKA PEMINDAHAN DATA (Penting untuk TiDB)
        if (Schema::hasColumn('materials', 'category')) {
            // Ambil nama kategori unik yang ada di TiDB saat ini
            $oldCategories = DB::table('materials')
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category');

            foreach ($oldCategories as $catName) {
                // Buat baris baru di tabel kategori baru jika belum ada
                $categoryId = DB::table('material_categories')->updateOrInsert(
                    ['name' => $catName],
                    ['description' => 'Materi tentang ' . $catName, 'created_at' => now(), 'updated_at' => now()]
                );

                // Ambil ID dari kategori tersebut
                $category = DB::table('material_categories')->where('name', $catName)->first();

                // Update tabel materials agar category_id terisi berdasarkan nama kategori lamanya
                DB::table('materials')
                    ->where('category', $catName)
                    ->update(['category_id' => $category->id]);
            }
        }

        // 3. SETELAH DATA AMAN PINDAH, baru hapus kolom string lama
        Schema::table('materials', function (Blueprint $table) {
            if (Schema::hasColumn('materials', 'category')) {
                $table->dropColumn('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'material_type']);
        });
    }
};