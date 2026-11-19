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
        $budi = User::where('email', 'budi@pinjamruang.test')->firstOrFail();
        $sari = User::where('email', 'sari@pinjamruang.test')->firstOrFail();
        $admin = User::where('email', 'admin@pinjamruang.test')->firstOrFail();
        $petugas = User::where('email', 'petugas@pinjamruang.test')->firstOrFail();
        $ruang101 = Room::where('kode', 'RK-101')->firstOrFail();
        $ruang102 = Room::where('kode', 'RK-102')->firstOrFail();
        $ruang201 = Room::where('kode', 'RK-201')->firstOrFail();
        $lab01 = Room::where('kode', 'LAB-01')->firstOrFail();
        $lab02 = Room::where('kode', 'LAB-02')->firstOrFail();
        $aula01 = Room::where('kode', 'AUL-01')->firstOrFail();
        $aula02 = Room::where('kode', 'AUL-02')->firstOrFail();
        $rapat01 = Room::where('kode', 'RR-01')->firstOrFail();

        $bookings = [
            [
                'user_id' => $budi->id,
                'room_id' => $ruang101->id,
                'tanggal' => now()->addDay()->toDateString(),
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
                'keperluan' => 'Uji peminjaman dan jadwal bentrok',
                'jumlah_peserta' => 30,
                'status' => 'disetujui',
                'catatan_admin' => 'Disetujui untuk digunakan pada jadwal yang telah ditentukan.',
                'approved_by' => $petugas->id,
            ],
            [
                'user_id' => $budi->id,
                'room_id' => $lab01->id,
                'tanggal' => now()->addDay()->toDateString(),
                'jam_mulai' => '13:00',
                'jam_selesai' => '15:00',
                'keperluan' => 'Praktikum organisasi',
                'jumlah_peserta' => 20,
                'status' => 'menunggu',
            ],
            [
                'user_id' => $sari->id,
                'room_id' => $aula01->id,
                'tanggal' => now()->addDays(2)->toDateString(),
                'jam_mulai' => '09:00',
                'jam_selesai' => '12:00',
                'keperluan' => 'Rapat organisasi',
                'jumlah_peserta' => 150,
                'status' => 'menunggu',
            ],
            [
                'user_id' => $sari->id,
                'room_id' => $rapat01->id,
                'tanggal' => now()->addDays(2)->toDateString(),
                'jam_mulai' => '10:00',
                'jam_selesai' => '11:00',
                'keperluan' => 'Rapat koordinasi',
                'jumlah_peserta' => 12,
                'status' => 'disetujui',
                'catatan_admin' => 'Rapat dosen disetujui oleh petugas ruang.',
                'approved_by' => $petugas->id,
            ],
            [
                'user_id' => $budi->id,
                'room_id' => $aula02->id,
                'tanggal' => now()->addDays(5)->toDateString(),
                'jam_mulai' => '14:00',
                'jam_selesai' => '16:00',
                'keperluan' => 'Kegiatan kecil organisasi',
                'jumlah_peserta' => 60,
                'status' => 'ditolak',
                'catatan_admin' => 'Pengajuan ditolak karena jadwal aula digunakan untuk kegiatan kampus.',
                'approved_by' => $admin->id,
            ],
            [
                'user_id' => $sari->id,
                'room_id' => $ruang102->id,
                'tanggal' => now()->subDay()->toDateString(),
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
                'keperluan' => 'Kegiatan pembelajaran',
                'jumlah_peserta' => 30,
                'status' => 'selesai',
                'catatan_admin' => 'Kegiatan telah selesai dilaksanakan.',
                'approved_by' => $petugas->id,
            ],
            [
                'user_id' => $budi->id,
                'room_id' => $lab02->id,
                'tanggal' => now()->addDay()->toDateString(),
                'jam_mulai' => '10:00',
                'jam_selesai' => '12:00',
                'keperluan' => 'Pelatihan jaringan',
                'jumlah_peserta' => 20,
                'status' => 'dibatalkan',
            ],
            [
                'user_id' => $sari->id,
                'room_id' => $ruang201->id,
                'tanggal' => now()->addDays(4)->toDateString(),
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:30',
                'keperluan' => 'Diskusi kelas',
                'jumlah_peserta' => 25,
                'status' => 'menunggu',
            ],
            [
                'user_id' => $budi->id,
                'room_id' => $ruang102->id,
                'tanggal' => now()->addDays(6)->toDateString(),
                'jam_mulai' => '13:00',
                'jam_selesai' => '15:00',
                'keperluan' => 'Workshop mahasiswa',
                'jumlah_peserta' => 35,
                'status' => 'disetujui',
                'catatan_admin' => 'Workshop disetujui untuk tanggal tersebut.',
                'approved_by' => $petugas->id,
            ],
            [
                'user_id' => $sari->id,
                'room_id' => $lab01->id,
                'tanggal' => now()->addDays(2)->toDateString(),
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
                'keperluan' => 'Praktikum pemrograman',
                'jumlah_peserta' => 30,
                'status' => 'disetujui',
                'catatan_admin' => 'Lab disetujui untuk kegiatan praktikum.',
                'approved_by' => $petugas->id,
            ],
        ];

        foreach ($bookings as $booking) {
            Booking::create($booking);
        }
    }
}
