<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan model Level sudah dibuat sebelumnya
        Level::create([
            'id' => 1,
            'level_name' => 'Dasar Fungsi Logika',
            'level_order' => 1,
            'category' => 'Logic'
        ]);

        Level::create([
            'id' => 2,
            'level_name' => 'Fungsi Logika Lanjutan',
            'level_order' => 2,
            'category' => 'Logic'
        ]);
    }
}