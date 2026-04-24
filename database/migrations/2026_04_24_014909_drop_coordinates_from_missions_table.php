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
        Schema::table('missions', function (Blueprint $table) {
            // Menghapus kolom yang tidak terpakai
            $table->dropColumn(['target_x', 'target_y']);
        });
    }

    public function down()
    {
        Schema::table('missions', function (Blueprint $table) {
            // Jika ingin rollback, kolom akan dibuat kembali
            $table->float('target_x')->nullable();
            $table->float('target_y')->nullable();
        });
    }
};
