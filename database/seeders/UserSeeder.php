<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        Akun uji (password semua: password):
        admin@pinjamruang.test    | Admin Sistem       | admin
        petugas@pinjamruang.test  | Petugas Ruang      | petugas
        budi@pinjamruang.test     | Budi Santoso       | peminjam
        sari@pinjamruang.test     | Sari Wulandari     | peminjam
        */
        User::updateOrCreate(
            ['email' => 'admin@pinjamruang.test'],
            [
                'name' => 'Admin Sistem',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'nim_nip' => 'ADM-001',
                'no_hp' => '081200000001',
            ]
        );

        User::updateOrCreate(
            ['email' => 'petugas@pinjamruang.test'],
            [
                'name' => 'Petugas Ruang',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'nim_nip' => 'PTG-001',
                'no_hp' => '081200000002',
            ]
        );

        User::updateOrCreate(
            ['email' => 'budi@pinjamruang.test'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'peminjam',
                'nim_nip' => '232410001',
                'no_hp' => '081200000003',
                'organisasi' => 'Himpunan Mahasiswa',
            ]
        );

        User::updateOrCreate(
            ['email' => 'sari@pinjamruang.test'],
            [
                'name' => 'Sari Wulandari',
                'password' => Hash::make('password'),
                'role' => 'peminjam',
                'nim_nip' => '232410002',
                'no_hp' => '081200000004',
                'organisasi' => 'UKM Seni',
            ]
        );
    }
}
