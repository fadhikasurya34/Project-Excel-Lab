<?php

namespace Database\Seeders;

use App\Models\Classroom; // Baris ini yang harus ditambahkan
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Classroom::create([
            'name' => 'X Akuntansi - SMK Negeri 2',
            'teacher_name' => 'Anggraini Mulwinda',
            'class_code' => 'EXCEL-SK1',
            'icon' => '🏛️'
        ]);

        Classroom::create([
            'name' => 'Lab Excel Tambahan',
            'teacher_name' => 'Fadhlan Surya',
            'class_code' => 'LAB-DASH',
            'icon' => '🧪'
        ]);
    }
}