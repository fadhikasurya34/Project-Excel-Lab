<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mission;

class MissionSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'mission_type' => 'Syntax Assembly',
                'question' => 'Susunlah rumus IF tunggal untuk mengecek jika sel A1 lebih besar dari 75 maka "LULUS", jika tidak "REMEDI".',
                'key_answer' => '=IF(A1>75;"LULUS";"REMEDI")',
                'max_score' => 100,
                'level_id' => 1, // Pastikan ID Level ini ada di tabel levels
            ],
            [
                'mission_type' => 'Point & Click',
                'question' => 'Klik sel yang merupakan referensi nilai rata-rata siswa pada tabel di atas.',
                'key_answer' => 'C2',
                'max_score' => 150,
                'level_id' => 1,
            ],
            [
                'mission_type' => 'Direct Typing',
                'question' => 'Ketikkan rumus logika AND untuk mengecek apakah sel B2 > 70 DAN C2 > 70.',
                'key_answer' => '=AND(B2>70;C2>70)',
                'max_score' => 200,
                'level_id' => 2,
            ],
        ];

        foreach ($data as $item) {
            Mission::create($item);
        }
    }
}