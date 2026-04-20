<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mission_steps', function (Blueprint $table) {
            $table->decimal('target_x', 8, 2)->nullable()->change();
            $table->decimal('target_y', 8, 2)->nullable()->change();
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
