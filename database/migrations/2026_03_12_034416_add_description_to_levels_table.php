<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('levels', function (Blueprint $blueprint) {
            // Tambahkan kolom description setelah kolom category
            $blueprint->text('description')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('levels', function (Blueprint $blueprint) {
            $blueprint->dropColumn('description');
        });
    }
};