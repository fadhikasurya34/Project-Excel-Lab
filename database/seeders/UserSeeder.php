<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Daftar data Admin yang ingin dibuat
        $admins = [
            [
                'name'  => 'Administrator Lab Utama',
                'email' => 'admin@gmail.com',
                'password' => 'password'
            ],
            [
                'name'  => 'Petugas Lab 1',
                'email' => 'petugas1@gmail.com',
                'password' => 'passwordlab123'
            ],
            [
                'name'  => 'Supervisor UNNES',
                'email' => 'supervisor@mail.com',
                'password' => 'unnes2026'
            ],
            // tambah data admin lain di sini...
        ];

        // 2. Loop data tersebut ke database
        foreach ($admins as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                    'role' => 'admin',
                    'email_verified_at' => now(),
                ]
            );

            // 3. Proteksi ekstra: Pastikan role tetap 'admin'
            if ($user->role !== 'admin') {
                $user->role = 'admin';
                $user->save();
            }
        }
    }
}