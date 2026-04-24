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
            // Cek dulu, kalau kolomnya ada baru dihapus
            if (Schema::hasColumn('missions', 'target_x')) {
                $table->dropColumn('target_x');
            }
            
            if (Schema::hasColumn('missions', 'target_y')) {
                $table->dropColumn('target_y');
            }
        });
    }

    public function down()
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->float('target_x')->nullable();
            $table->float('target_y')->nullable();
        });
    }
};
