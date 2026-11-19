<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $peminjam1 = User::where('email', 'peminjam1@kampus.ac.id')->firstOrFail();
        $peminjam2 = User::where('email', 'peminjam2@kampus.ac.id')->firstOrFail();
        $admin = User::where('email', 'admin@kampus.ac.id')->firstOrFail();
        $petugas = User::where('email', 'petugas@kampus.ac.id')->firstOrFail();
        $ruang101 = Room::where('kode', 'RK-101')->firstOrFail();
        $lab = Room::where('kode', 'LAB-101')->firstOrFail();
        $aula = Room::where('kode', 'AULA-UTAMA')->firstOrFail();

        $bookings = [
            [
                'user_id' => $peminjam1->id,
                'room_id' => $ruang101->id,
                'tanggal' => now()->toDateString(),
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
                'keperluan' => 'Kuliah tamu',
                'jumlah_peserta' => 30,
                'status' => 'menunggu',
            ],
            [
                'user_id' => $peminjam2->id,
                'room_id' => $lab->id,
                'tanggal' => now()->addDay()->toDateString(),
                'jam_mulai' => '09:00',
                'jam_selesai' => '12:00',
                'keperluan' => 'Praktikum organisasi',
                'jumlah_peserta' => 20,
                'status' => 'disetujui',
                'catatan_admin' => 'Disetujui untuk kegiatan praktik.',
                'approved_by' => $admin->id,
            ],
            [
                'user_id' => $peminjam1->id,
                'room_id' => $aula->id,
                'tanggal' => now()->addDays(2)->toDateString(),
                'jam_mulai' => '13:00',
                'jam_selesai' => '16:00',
                'keperluan' => 'Acara UKM',
                'jumlah_peserta' => 150,
                'status' => 'ditolak',
                'catatan_admin' => 'Aula digunakan untuk kegiatan kampus.',
                'approved_by' => $petugas->id,
            ],
            [
                'user_id' => $peminjam2->id,
                'room_id' => $ruang101->id,
                'tanggal' => now()->subDay()->toDateString(),
                'jam_mulai' => '10:00',
                'jam_selesai' => '12:00',
                'keperluan' => 'Rapat pengurus',
                'jumlah_peserta' => 25,
                'status' => 'dibatalkan',
            ],
            [
                'user_id' => $peminjam1->id,
                'room_id' => $lab->id,
                'tanggal' => now()->subDays(2)->toDateString(),
                'jam_mulai' => '13:00',
                'jam_selesai' => '15:00',
                'keperluan' => 'Pelatihan',
                'jumlah_peserta' => 18,
                'status' => 'selesai',
                'approved_by' => $petugas->id,
            ],
        ];

        foreach ($bookings as $booking) {
            Booking::create($booking);
        }
    }
}
