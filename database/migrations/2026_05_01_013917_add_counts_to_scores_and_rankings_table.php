<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scores_and_rankings', function (Blueprint $table) {
            // Menambahkan kolom baru tanpa menghapus data lama
            $table->integer('completed_missions_count')->default(0)->after('total_xp');
            $table->integer('completed_modules_count')->default(0)->after('completed_missions_count');
        });
    }

    public function down(): void
    {
        Schema::table('scores_and_rankings', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback
            $table->dropColumn(['completed_missions_count', 'completed_modules_count']);
        });
    }
};