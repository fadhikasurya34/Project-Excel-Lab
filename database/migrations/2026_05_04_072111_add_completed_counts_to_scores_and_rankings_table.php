<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('scores_and_rankings', function (Blueprint $table) {
            // Cek dulu apakah kolom sudah ada sebelum menambahkannya
            if (!Schema::hasColumn('scores_and_rankings', 'completed_missions_count')) {
                $table->integer('completed_missions_count')->default(0)->after('total_xp');
            }

            if (!Schema::hasColumn('scores_and_rankings', 'completed_modules_count')) {
                $table->integer('completed_modules_count')->default(0)->after('completed_missions_count');
            }
        });
    }

    public function down()
    {
        Schema::table('scores_and_rankings', function (Blueprint $table) {
            // Rollback hanya jika kolom memang ada
            $columns = [];
            if (Schema::hasColumn('scores_and_rankings', 'completed_missions_count')) $columns[] = 'completed_missions_count';
            if (Schema::hasColumn('scores_and_rankings', 'completed_modules_count')) $columns[] = 'completed_modules_count';
            
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};