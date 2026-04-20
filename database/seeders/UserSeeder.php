<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Bersihkan data user lama agar tidak duplikat saat seeding ulang
        // DB::table('users')->truncate(); // Opsional: gunakan jika ingin reset total

        // 2. Membuat akun Admin Utama
        // Kita menggunakan updateOrCreate agar jika seeder dijalankan ulang, 
        // akun ini tidak error (Unique constraint email)
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // Identifier
            [
                'name' => 'Administrator Lab',
                'password' => Hash::make('password'),
                'role' => 'admin', // Ini akan tetap 'admin' jika mass assignment benar
                'email_verified_at' => now(), // Tambahkan ini agar lolos middleware 'verified'
            ]
        );

        // Tips: Jika kamu ingin memastikan role tidak berubah oleh Model Boot, 
        // kamu bisa memaksa update role setelah create:
        $admin = User::where('email', 'admin@gmail.com')->first();
        if ($admin->role !== 'admin') {
            $admin->role = 'admin';
            $admin->save();
        }
    }
}