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
            ['RK-101', 'Ruang Kelas 101', 40, 'Gedung A Lt.1', 'tersedia', 'Ruang Kelas'],
            ['RK-102', 'Ruang Kelas 102', 40, 'Gedung A Lt.1', 'tersedia', 'Ruang Kelas'],
            ['RK-201', 'Ruang Kelas 201', 30, 'Gedung A Lt.2', 'tersedia', 'Ruang Kelas'],
            ['LAB-01', 'Lab Komputer 1', 35, 'Gedung B Lt.1', 'tersedia', 'Laboratorium'],
            ['LAB-02', 'Lab Jaringan', 25, 'Gedung B Lt.2', 'tersedia', 'Laboratorium'],
            ['LAB-03', 'Lab Multimedia', 25, 'Gedung B Lt.2', 'perbaikan', 'Laboratorium'],
            ['AUL-01', 'Aula Utama', 200, 'Gedung C', 'tersedia', 'Aula'],
            ['AUL-02', 'Aula Mini', 80, 'Gedung C', 'tersedia', 'Aula'],
            ['RR-01', 'Ruang Rapat Dosen', 15, 'Gedung A Lt.3', 'tersedia', 'Ruang Rapat'],
            ['RR-02', 'Ruang Rapat Organisasi', 20, 'Gedung D', 'nonaktif', 'Ruang Rapat'],
        ];

        foreach ($rooms as [$kode, $nama, $kapasitas, $lokasi, $status, $kategori]) {
            Room::updateOrCreate(
                ['kode' => $kode],
                [
                    'room_category_id' => $categories[$kategori]->id,
                    'nama' => $nama,
                    'kapasitas' => $kapasitas,
                    'lokasi' => $lokasi,
                    'fasilitas' => 'Proyektor, AC, dan papan tulis',
                    'deskripsi' => 'Ruang kampus siap digunakan sesuai jadwal.',
                    'status' => $status,
                ]
            );
        }
    }
}
