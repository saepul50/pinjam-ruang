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
            ['RK-101', 'Ruang Kelas 101', 40, 'Gedung A', 1, 1, 'Sayap kiri', 'Proyektor, AC, Whiteboard', 'tersedia', 'Ruang Kelas'],
            ['RK-102', 'Ruang Kelas 102', 40, 'Gedung A', 1, 2, 'Sayap kanan', 'Proyektor, AC, Speaker', 'tersedia', 'Ruang Kelas'],
            ['RK-201', 'Ruang Kelas 201', 30, 'Gedung A', 2, 1, 'Dekat tangga', 'Proyektor, AC', 'tersedia', 'Ruang Kelas'],
            ['RR-01', 'Ruang Rapat Dosen', 15, 'Gedung A', 3, 1, 'Ujung koridor', 'Video Conference, Wi-Fi, AC', 'tersedia', 'Ruang Rapat'],
            ['LAB-01', 'Lab Komputer 1', 35, 'Gedung B', 1, 1, 'Sayap kiri', '35 Komputer, LAN, Proyektor', 'tersedia', 'Laboratorium'],
            ['LAB-02', 'Lab Jaringan', 25, 'Gedung B', 2, 1, 'Sayap kiri', 'Perangkat jaringan, LAN, AC', 'tersedia', 'Laboratorium'],
            ['LAB-03', 'Lab Multimedia', 25, 'Gedung B', 2, 2, 'Sayap kanan', 'Kamera, Lighting, Audio', 'perbaikan', 'Laboratorium'],
            ['AUL-01', 'Aula Utama', 200, 'Gedung C', 1, 1, 'Area utama', 'Sound System, Proyektor, AC', 'tersedia', 'Aula'],
            ['AUL-02', 'Aula Mini', 80, 'Gedung C', 2, 1, 'Area utama', 'Sound System, Proyektor, AC', 'tersedia', 'Aula'],
            ['RR-02', 'Ruang Rapat Organisasi', 20, 'Gedung D', 1, 1, 'Sayap kiri', 'Wi-Fi, Smart TV, Whiteboard', 'nonaktif', 'Ruang Rapat'],
        ];

        foreach ($rooms as [$kode, $nama, $kapasitas, $gedung, $lantai, $posisi, $lokasi, $fasilitas, $status, $kategori]) {
            Room::updateOrCreate(
                ['kode' => $kode],
                [
                    'room_category_id' => $categories[$kategori]->id,
                    'nama' => $nama,
                    'kapasitas' => $kapasitas,
                    'lokasi' => $lokasi,
                    'gedung' => $gedung,
                    'lantai' => $lantai,
                    'posisi_denah' => $posisi,
                    'foto_url' => null,
                    'fasilitas' => $fasilitas,
                    'deskripsi' => 'Ruang kampus siap digunakan sesuai jadwal.',
                    'status' => $status,
                ]
            );
        }
    }
}
