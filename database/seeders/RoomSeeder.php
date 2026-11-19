<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomCategory;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $categories = RoomCategory::whereIn('nama', [
            'Ruang Kelas',
            'Laboratorium',
            'Aula',
            'Ruang Rapat',
        ])->get()->keyBy('nama');

        $rooms = [
            ['RK-101', 'Ruang Kelas 101', 40, 'Gedung Fakultas A', 1, 48, 'tersedia', 'Ruang Kelas'],
            ['RK-102', 'Ruang Kelas 102', 40, 'Gedung Fakultas A', 1, 48, 'tersedia', 'Ruang Kelas'],
            ['RK-201', 'Ruang Kelas 201', 30, 'Gedung Fakultas A', 2, 36, 'tersedia', 'Ruang Kelas'],
            ['LAB-01', 'Lab Komputer 1', 35, 'Gedung Fakultas B', 1, 60, 'tersedia', 'Laboratorium'],
            ['LAB-02', 'Lab Jaringan', 25, 'Gedung Fakultas B', 2, 50, 'tersedia', 'Laboratorium'],
            ['LAB-03', 'Lab Multimedia', 25, 'Gedung Fakultas B', 2, 55, 'perbaikan', 'Laboratorium'],
            ['AUL-01', 'Aula Utama', 200, 'Gedung Serbaguna C', 1, 320, 'tersedia', 'Aula'],
            ['AUL-02', 'Aula Mini', 80, 'Gedung Serbaguna C', 2, 140, 'tersedia', 'Aula'],
            ['RR-01', 'Ruang Rapat Dosen', 15, 'Gedung Fakultas A', 3, 28, 'tersedia', 'Ruang Rapat'],
            ['RR-02', 'Ruang Rapat Organisasi', 20, 'Gedung Fakultas D', 2, 35, 'nonaktif', 'Ruang Rapat'],
        ];

        foreach ($rooms as [$kode, $nama, $kapasitas, $gedung, $lantai, $luas, $status, $kategori]) {
            Room::updateOrCreate(
                ['kode' => $kode],
                [
                    'room_category_id' => $categories[$kategori]->id,
                    'nama' => $nama,
                    'kapasitas' => $kapasitas,
                    'lokasi' => $gedung . ' Lantai ' . $lantai,
                    'gedung' => $gedung,
                    'lantai' => $lantai,
                    'luas_m2' => $luas,
                    'foto_url' => null,
                    'fasilitas' => 'Proyektor, AC, Papan tulis',
                    'deskripsi' => 'Ruang kampus siap digunakan sesuai jadwal.',
                    'status' => $status,
                ]
            );
        }
    }
}
