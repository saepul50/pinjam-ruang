<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Semua akun uji menggunakan password: password
        User::updateOrCreate(
            ['email' => 'admin@kampus.ac.id'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'nim_nip' => 'ADM-001',
                'no_hp' => '081200000001',
            ]
        );

        User::updateOrCreate(
            ['email' => 'petugas@kampus.ac.id'],
            [
                'name' => 'Petugas Ruang',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'nim_nip' => 'PTG-001',
                'no_hp' => '081200000002',
            ]
        );

        User::updateOrCreate(
            ['email' => 'peminjam1@kampus.ac.id'],
            [
                'name' => 'Peminjam Satu',
                'password' => Hash::make('password'),
                'role' => 'peminjam',
                'nim_nip' => '232410001',
                'no_hp' => '081200000003',
                'organisasi' => 'Himpunan Mahasiswa',
            ]
        );

        User::updateOrCreate(
            ['email' => 'peminjam2@kampus.ac.id'],
            [
                'name' => 'Peminjam Dua',
                'password' => Hash::make('password'),
                'role' => 'peminjam',
                'nim_nip' => '232410002',
                'no_hp' => '081200000004',
                'organisasi' => 'UKM Seni',
            ]
        );
    }
}
