<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_peminjam_dashboard_has_home_menu(): void
    {
        $peminjam = $this->createUser('peminjam');

        $this->actingAs($peminjam)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Beranda')
            ->assertSee('Jelajahi')
            ->assertSee('Daftar Ruang')
            ->assertSee('Cara Booking')
            ->assertSee('Peminjaman Saya')
            ->assertSee('aria-controls="sidebar-content"', false)
            ->assertSee('class="nav-backdrop"', false)
            ->assertSee('Pinjam ruang kampus jadi lebih mudah.')
            ->assertDontSee('>Dashboard</a>', false);
    }

    public function test_peminjam_can_read_booking_guide(): void
    {
        $peminjam = $this->createUser('peminjam');

        $this->actingAs($peminjam)
            ->get('/cara-booking')
            ->assertOk()
            ->assertSee('Empat langkah mudah dari memilih ruang sampai pengajuan disetujui.')
            ->assertSee('Arti status pengajuan')
            ->assertSee('Sebelum mengajukan')
            ->assertSee('Lihat daftar ruang');
    }

    public function test_peminjam_can_explore_rooms_on_an_interactive_floor_plan(): void
    {
        $peminjam = $this->createUser('peminjam');
        $room = $this->createRoom();

        $response = $this->actingAs($peminjam)
            ->get('/rooms')
            ->assertOk()
            ->assertSee('floorplan-svg', false)
            ->assertSee('floor-room is-off', false)
            ->assertDontSee('room-furniture', false)
            ->assertDontSee('plan-lobby', false)
            ->assertSee('TOILET PRIA')
            ->assertSee('TOILET WANITA')
            ->assertSee('TANGGA')
            ->assertSee('LIFT')
            ->assertDontSee('LOBI')
            ->assertDontSee('AREA BERSAMA')
            ->assertSee('Ruang Uji')
            ->assertSee('room-status-dot is-available', false)
            ->assertSee('room_id=' . $room->id, false);

        $this->assertSame(7, substr_count($response->getContent(), 'class="floor-room '));
    }

    public function test_long_room_names_wrap_inside_the_floor_plan_room(): void
    {
        $peminjam = $this->createUser('peminjam');
        $room = $this->createRoom();
        $room->update(['nama' => 'Ruang Kelas Laboratorium Komputer 204']);

        $response = $this->actingAs($peminjam)
            ->get('/rooms')
            ->assertOk()
            ->assertSee('Ruang Kelas')
            ->assertSee('Laboratorium')
            ->assertSee('Komputer 204');

        $this->assertSame(3, substr_count($response->getContent(), '<tspan'));
    }

    public function test_floor_plan_shows_room_booking_status_dots(): void
    {
        $peminjam = $this->createUser('peminjam');
        $room = $this->createRoom();
        $otherRoom = Room::create([
            'room_category_id' => $room->room_category_id,
            'kode' => 'RK-RAWAT',
            'nama' => 'Ruang Perawatan',
            'kapasitas' => 20,
            'lokasi' => 'Sayap kanan',
            'gedung' => $room->gedung,
            'lantai' => $room->lantai,
            'posisi_denah' => 2,
            'status' => 'perbaikan',
        ]);
        Booking::create([
            'user_id' => $peminjam->id,
            'room_id' => $room->id,
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
            'keperluan' => 'Kegiatan uji',
            'status' => 'disetujui',
        ]);

        $this->actingAs($peminjam)
            ->get('/rooms')
            ->assertOk()
            ->assertSee('room-status-dot is-used', false)
            ->assertSee('room-status-dot is-maintenance', false)
            ->assertSee('Digunakan hari ini')
            ->assertSee('Dalam perawatan');
    }

    public function test_floor_plan_shows_rooms_from_all_buildings_by_floor(): void
    {
        $peminjam = $this->createUser('peminjam');
        $category = RoomCategory::create([
            'nama' => 'Kategori Seluruh Kampus',
            'deskripsi' => 'Kategori pengujian jumlah ruang lintas gedung',
        ]);

        foreach ([1 => 5, 2 => 4, 3 => 1] as $lantai => $jumlahRuang) {
            for ($nomor = 1; $nomor <= $jumlahRuang; $nomor++) {
                Room::create([
                    'room_category_id' => $category->id,
                    'kode' => "RK-L{$lantai}-{$nomor}",
                    'nama' => "Ruang Lantai {$lantai} Nomor {$nomor}",
                    'kapasitas' => 20,
                    'lokasi' => 'Koridor',
                    'gedung' => 'Gedung ' . chr(64 + $nomor),
                    'lantai' => $lantai,
                    'posisi_denah' => 1,
                    'status' => 'tersedia',
                ]);
            }
        }

        foreach ([1 => 5, 2 => 4, 3 => 1] as $lantai => $jumlahRuang) {
            $response = $this->actingAs($peminjam)
                ->get('/rooms?lantai=' . $lantai)
                ->assertOk()
                ->assertSee("{$jumlahRuang} ruang di lantai {$lantai}")
                ->assertSee('Denah seluruh gedung, lantai ' . $lantai, false);

            $this->assertSame(
                $jumlahRuang,
                substr_count($response->getContent(), 'class="floor-room-link"')
            );
            $this->assertSame(7, substr_count($response->getContent(), 'class="floor-room '));
        }
    }

    public function test_room_slot_seven_is_available_for_a_floor_plan(): void
    {
        $admin = $this->createUser('admin', 'admin-slot-seven@kampus.ac.id');
        $roomCategory = RoomCategory::create([
            'nama' => 'Kategori Slot Tujuh',
            'deskripsi' => 'Kategori pengujian denah tujuh ruang',
        ]);
        $login = $this->postJson('/api/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->withToken($login->json('token'))->postJson('/api/rooms', [
            'room_category_id' => $roomCategory->id,
            'kode' => 'RK-SLOT-7',
            'nama' => 'Ruang Slot Tujuh',
            'kapasitas' => 20,
            'lokasi' => 'Sayap kanan',
            'gedung' => 'Gedung Tujuh',
            'lantai' => 1,
            'posisi_denah' => 7,
            'status' => 'tersedia',
        ])->assertCreated()
            ->assertJsonPath('data.posisi_denah', 7);
    }

    public function test_petugas_dashboard_has_dashboard_menu(): void
    {
        $petugas = $this->createUser('petugas', 'petugas-navbar@kampus.ac.id');

        $this->actingAs($petugas)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertDontSee('Jelajahi Ruang');
    }

    public function test_petugas_cannot_open_booking_form(): void
    {
        $petugas = $this->createUser('petugas', 'petugas-web@kampus.ac.id');

        $this->actingAs($petugas)
            ->get('/bookings/create')
            ->assertForbidden();
    }

    public function test_booking_requires_authentication(): void
    {
        $this->getJson('/api/bookings')
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Autentikasi diperlukan.',
            ]);
    }

    public function test_peminjam_can_login_and_create_booking(): void
    {
        $peminjam = $this->createUser('peminjam');
        $room = $this->createRoom();

        $login = $this->postJson('/api/login', [
            'email' => $peminjam->email,
            'password' => 'password',
        ]);

        $login->assertOk()
            ->assertJsonStructure(['token', 'user']);

        $token = $login->json('token');

        $this->withToken($token)->postJson('/api/bookings', [
            'room_id' => $room->id,
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '11:00',
            'jam_selesai' => '12:00',
            'keperluan' => 'Rapat organisasi',
            'jumlah_peserta' => 10,
        ])->assertCreated()
            ->assertJsonPath('data.status', 'menunggu');
    }

    public function test_petugas_cannot_create_booking(): void
    {
        $petugas = $this->createUser('petugas', 'petugas-uji@kampus.ac.id');
        $room = $this->createRoom();

        $login = $this->postJson('/api/login', [
            'email' => $petugas->email,
            'password' => 'password',
        ]);

        $this->withToken($login->json('token'))->postJson('/api/bookings', [
            'room_id' => $room->id,
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '11:00',
            'jam_selesai' => '12:00',
            'keperluan' => 'Pengajuan oleh petugas',
            'jumlah_peserta' => 10,
        ])->assertForbidden();
    }

    public function test_room_requires_floor(): void
    {
        $admin = $this->createUser('admin', 'admin-room@kampus.ac.id');
        $login = $this->postJson('/api/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);
        $roomCategory = RoomCategory::create([
            'nama' => 'Kategori Lantai',
            'deskripsi' => 'Kategori pengujian lantai',
        ]);

        $this->withToken($login->json('token'))->postJson('/api/rooms', [
            'room_category_id' => $roomCategory->id,
            'kode' => 'RK-NOLANTAI',
            'nama' => 'Tanpa Lantai',
            'kapasitas' => 20,
            'lokasi' => 'Sayap kiri',
            'gedung' => 'Gedung A',
            'status' => 'tersedia',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['lantai']);
    }

    public function test_room_rejects_used_floor_slot(): void
    {
        $admin = $this->createUser('admin', 'admin-slot@kampus.ac.id');
        $login = $this->postJson('/api/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);
        $roomCategory = RoomCategory::create([
            'nama' => 'Kategori Slot',
            'deskripsi' => 'Kategori pengujian slot',
        ]);
        $this->createRoom();
        $existing = Room::where('kode', 'RK-UJI')->first();

        $this->withToken($login->json('token'))->postJson('/api/rooms', [
            'room_category_id' => $roomCategory->id,
            'kode' => 'RK-SLOT',
            'nama' => 'Slot Bentrok',
            'kapasitas' => 20,
            'lokasi' => 'Sayap kiri',
            'gedung' => $existing->gedung,
            'lantai' => $existing->lantai,
            'posisi_denah' => $existing->posisi_denah,
            'status' => 'tersedia',
        ])->assertUnprocessable();
    }

    public function test_room_filter_by_floor_returns_seeded_rooms(): void
    {
        $this->createRoomFloorData();

        $this->getJson('/api/rooms?lantai=2')
            ->assertOk()
            ->assertJsonCount(4, 'data.data');
    }

    public function test_overlapping_booking_is_rejected(): void
    {
        $peminjam = $this->createUser('peminjam');
        $room = $this->createRoom();

        Booking::create([
            'user_id' => $peminjam->id,
            'room_id' => $room->id,
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'keperluan' => 'Booking aktif',
            'jumlah_peserta' => 10,
            'status' => 'menunggu',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => $peminjam->email,
            'password' => 'password',
        ]);

        $this->withToken($login->json('token'))->postJson('/api/bookings', [
            'room_id' => $room->id,
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '09:00',
            'jam_selesai' => '11:00',
            'keperluan' => 'Pengajuan bentrok',
            'jumlah_peserta' => 10,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['jam_mulai']);
    }

    public function test_peminjam_cannot_access_another_users_booking(): void
    {
        $pemilik = $this->createUser('peminjam', 'pemilik@kampus.ac.id');
        $pengakses = $this->createUser('peminjam', 'pengakses@kampus.ac.id');
        $room = $this->createRoom();

        $booking = Booking::create([
            'user_id' => $pemilik->id,
            'room_id' => $room->id,
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:00',
            'keperluan' => 'Rapat pemilik',
            'status' => 'menunggu',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => $pengakses->email,
            'password' => 'password',
        ]);

        $this->withToken($login->json('token'))
            ->getJson('/api/bookings/' . $booking->id)
            ->assertForbidden();
    }

    private function createUser(string $role, ?string $email = null): User
    {
        $user = new User([
            'name' => 'Pengguna Uji',
            'email' => $email ?? 'peminjam@kampus.ac.id',
            'password' => Hash::make('password'),
        ]);
        $user->role = $role;
        $user->save();

        return $user;
    }

    private function createRoom(): Room
    {
        $category = RoomCategory::create([
            'nama' => 'Ruang Uji',
            'deskripsi' => 'Kategori untuk pengujian',
        ]);

        return Room::create([
            'room_category_id' => $category->id,
            'kode' => 'RK-UJI',
            'nama' => 'Ruang Uji',
            'kapasitas' => 30,
            'lokasi' => 'Sayap kiri',
            'gedung' => 'Gedung Uji',
            'lantai' => 2,
            'posisi_denah' => 1,
            'status' => 'tersedia',
        ]);
    }

    private function createRoomFloorData(): void
    {
        $category = RoomCategory::create([
            'nama' => 'Kategori lantai 2',
            'deskripsi' => 'Kategori pengujian filter lantai',
        ]);

        foreach ([
            ['RK-201', 'Ruang Kelas 201', 1],
            ['LAB-02', 'Lab Jaringan', 2],
            ['LAB-03', 'Lab Multimedia', 3],
            ['AUL-02', 'Aula Mini', 4],
        ] as [$kode, $nama, $posisi]) {
            Room::create([
                'room_category_id' => $category->id,
                'kode' => $kode,
                'nama' => $nama,
                'kapasitas' => 30,
                'lokasi' => 'Lantai dua',
                'gedung' => 'Gedung Uji',
                'lantai' => 2,
                'posisi_denah' => $posisi,
                'status' => 'tersedia',
            ]);
        }
    }
}
