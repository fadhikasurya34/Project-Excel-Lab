<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('scores_and_rankings', function (Blueprint $table) {
            // Tambahkan kolom yang hilang
            $table->integer('completed_missions_count')->default(0)->after('total_xp');
            $table->integer('completed_modules_count')->default(0)->after('completed_missions_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scores_and_rankings', function (Blueprint $table) {
            //
        });
    }
};
