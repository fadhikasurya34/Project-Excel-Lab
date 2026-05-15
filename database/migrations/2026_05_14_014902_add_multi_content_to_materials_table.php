<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('materials', function (Blueprint $table) {
            // Menambahkan 3 kolom baru setelah kolom material_type
            $table->string('video_url')->nullable()->after('material_type');
            $table->longText('text_content')->nullable()->after('video_url');
            $table->string('pdf_url')->nullable()->after('text_content');
        });
    }

    public function down()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['video_url', 'text_content', 'pdf_url']);
        });
    }
};