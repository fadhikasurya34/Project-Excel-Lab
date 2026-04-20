<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // Memperbaiki tabel materials agar background_image boleh kosong (karena gambar ada di langkah)
        Schema::table('materials', function (Blueprint $table) {
            $table->string('background_image')->nullable()->change();
        });

        // Memperbaiki tabel material_activities agar koordinat boleh kosong saat awal upload gambar
        Schema::table('material_activities', function (Blueprint $table) {
            $table->decimal('x_percent', 5, 2)->nullable()->change();
            $table->decimal('y_percent', 5, 2)->nullable()->change();
            $table->text('explanation_content')->nullable()->change();
        });
    }

    public function down() {}
};