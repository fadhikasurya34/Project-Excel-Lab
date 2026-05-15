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
        Schema::table('material_comments', function (Blueprint $table) {
            // parent_id untuk reply (nullable karena komen utama tidak punya parent)
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('material_comments')->cascadeOnDelete();
            $table->integer('likes')->default(0);
            $table->integer('dislikes')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_comments', function (Blueprint $table) {
            //
        });
    }
};
