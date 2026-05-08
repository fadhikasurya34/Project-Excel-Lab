<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. BUAT TABEL KATEGORI DULU (Ini yang kelupaan tadi!)
        if (!Schema::hasTable('material_categories')) {
            Schema::create('material_categories', function (Blueprint $table) {
                $idType = config('database.default') === 'sqlite' ? 'integer' : 'bigint';
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 2. TAMBAH KOLOM BARU DI MATERIALS
        if (!Schema::hasColumn('materials', 'category_id')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->foreignId('category_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('material_categories')
                      ->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('materials', 'material_type')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->string('material_type')->default('teori')->after('description');
            });
        }

        // 3. LOGIKA PEMINDAHAN DATA (Menyelamatkan data TiDB)
        if (Schema::hasColumn('materials', 'category')) {
            $oldCategories = DB::table('materials')
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category');

            foreach ($oldCategories as $catName) {
                // Buat kategori jika belum ada
                DB::table('material_categories')->updateOrInsert(
                    ['name' => $catName],
                    ['description' => 'Folder materi ' . $catName, 'created_at' => now(), 'updated_at' => now()]
                );

                $category = DB::table('material_categories')->where('name', $catName)->first();

                // Update materials
                DB::table('materials')
                    ->where('category', $catName)
                    ->update(['category_id' => $category->id]);
            }
        }

        // 4. HAPUS KOLOM LAMA SETELAH AMAN
        if (Schema::hasColumn('materials', 'category')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('materials', 'category')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->string('category')->nullable();
            });
        }

        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'material_type']);
        });

        Schema::dropIfExists('material_categories');
    }
};