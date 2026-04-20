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
    Schema::table('users', function (Blueprint $table) {
        // Menghapus kolom yang sudah tidak terpakai
        $table->dropColumn(['remedial_tickets', 'last_regen_date']);
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        // Logika rollback: buat kembali kolom jika migration di-cancel
        $table->integer('remedial_tickets')->default(3);
        $table->date('last_regen_date')->nullable();
    });
}
};
