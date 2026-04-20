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
        Schema::table('material_activities', function (Blueprint $table) {
            // Mengubah kolom menjadi nullable agar tidak error saat awal upload gambar
            $table->decimal('x_percent', 5, 2)->nullable()->change();
            $table->decimal('y_percent', 5, 2)->nullable()->change();
            $table->text('explanation_content')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
