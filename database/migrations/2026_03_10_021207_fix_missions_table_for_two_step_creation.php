<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('missions', function (Blueprint $table) {
            // Menambahkan kolom title jika belum ada
            if (!Schema::hasColumn('missions', 'title')) {
                $table->string('title')->nullable()->after('id');
            }
            
            // Mengubah kolom agar boleh null (untuk proses tahap 1)
            $table->text('question')->nullable()->change();
            $table->text('key_answer')->nullable()->change();
        });
    }

    public function down(): void {
        Schema::table('missions', function (Blueprint $table) {
            $table->text('question')->nullable(false)->change();
            $table->text('key_answer')->nullable(false)->change();
        });
    }
};