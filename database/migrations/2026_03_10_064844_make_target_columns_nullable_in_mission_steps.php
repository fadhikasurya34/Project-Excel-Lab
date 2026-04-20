<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('mission_steps', function (Blueprint $table) {
            $table->float('target_x')->nullable()->change();
            $table->float('target_y')->nullable()->change();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mission_steps', function (Blueprint $table) {
            //
        });
    }
};
