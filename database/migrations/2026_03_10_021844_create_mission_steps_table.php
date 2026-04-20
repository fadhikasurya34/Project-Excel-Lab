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
    // database/migrations/xxxx_create_mission_steps_table.php
    Schema::create('mission_steps', function (Blueprint $table) {
        $table->id();
        $table->foreignId('mission_id')->constrained()->onDelete('cascade');
        $table->string('step_image');
        $table->text('instruction');
        $table->string('key_answer_cell'); // Contoh: B2
        $table->float('target_x');
        $table->float('target_y');
        $table->integer('step_order')->default(1);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_steps');
    }
};
