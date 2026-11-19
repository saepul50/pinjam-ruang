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
            ['kelas', 'RK-101', 'Ruang Kelas 101', 40, 'Gedung A Lantai 1', 'tersedia', 'Ruang Kelas'],
            ['kelas', 'RK-102', 'Ruang Kelas 102', 35, 'Gedung A Lantai 1', 'tersedia', 'Ruang Kelas'],
            ['lab', 'LAB-101', 'Laboratorium Komputer 1', 30, 'Gedung B Lantai 2', 'tersedia', 'Laboratorium'],
            ['lab', 'LAB-201', 'Laboratorium Komputer 2', 25, 'Gedung B Lantai 2', 'perbaikan', 'Laboratorium'],
            ['lab', 'LAB-FIS', 'Laboratorium Fisika', 20, 'Gedung C Lantai 1', 'tersedia', 'Laboratorium'],
            ['aula', 'AULA-UTAMA', 'Aula Utama', 200, 'Gedung Serbaguna', 'tersedia', 'Aula'],
            ['rapat', 'RR-301', 'Ruang Rapat 301', 16, 'Gedung A Lantai 3', 'tersedia', 'Ruang Rapat'],
            ['rapat', 'RR-302', 'Ruang Rapat 302', 12, 'Gedung A Lantai 3', 'nonaktif', 'Ruang Rapat'],
            ['kelas', 'RK-201', 'Ruang Kelas 201', 45, 'Gedung A Lantai 2', 'tersedia', 'Ruang Kelas'],
        ];

        foreach ($rooms as [$slug, $kode, $nama, $kapasitas, $lokasi, $status, $kategori]) {
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
