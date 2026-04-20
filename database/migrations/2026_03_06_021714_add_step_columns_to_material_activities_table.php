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
        Schema::table('material_activities', function (Blueprint $table) {
                // Kolom untuk menyimpan gambar per langkah (instruksional)
                $table->string('step_image')->after('material_id')->nullable();
                // Kolom untuk menentukan urutan (Langkah 1, 2, 3...)
                $table->integer('step_order')->after('step_image')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_activities', function (Blueprint $table) {
            //
        });
    }
};