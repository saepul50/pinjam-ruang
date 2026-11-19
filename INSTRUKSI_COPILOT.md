# INSTRUKSI COPILOT: Backend Proyek `pinjam-ruang`

> Salin seluruh isi file ini ke Copilot Chat (mode Agent) yang dibuka di folder proyek `pinjam-ruang`.
> Cakupan: HANYA BACKEND (migration, model, controller, request validation, route, seeder, API).
> Frontend/Blade akan dikerjakan belakangan setelah semua endpoint lolos uji di Postman.

---

## 1. Konteks

- Proyek baru: `pinjam-ruang` (Laravel 12), sistem peminjaman ruang kampus.
- Proyek referensi: `app-perpustakaan`, berada satu tingkat di atas folder ini (folder sejajar). Kodenya berasal dari materi dosen (pertemuan 01 sampai 11) dan menjadi PATOKAN gaya penulisan.
- Tugasmu: mengonversi pola kode `app-perpustakaan` menjadi versi `pinjam-ruang`, bukan menulis dengan gaya sendiri.

## 2. Aturan Wajib

1. Jangan mengubah apa pun di folder `app-perpustakaan` (hanya dibaca).
2. Ikuti gaya penulisan, penamaan, struktur folder, dan pola validasi yang ada di `app-perpustakaan`.
3. Jangan membuat atau mengubah file frontend (Blade, CSS, JS). Jika controller web membutuhkan view, cukup `return view(...)` dengan nama view yang sesuai pola, tanpa membuat file view-nya.
4. Setelah setiap tahap, jalankan `php artisan route:list` dan `php artisan migrate:fresh --seed` untuk memastikan tidak ada error.
5. Jangan menginstal paket baru kecuali `app-perpustakaan` juga memakainya (misalnya Sanctum jika dipakai di sana).
6. Gunakan bahasa Indonesia untuk komentar, pesan validasi, dan pesan respons.

## 3. Langkah 0: Masuk ke Proyek Referensi

Jalankan perintah ini di terminal (posisi awal: di dalam folder `pinjam-ruang`):

```bash
cd ..
cd app-perpustakaan
```

Lalu pelajari isinya (baca saja):

```bash
ls app/Http/Controllers
ls app/Http/Controllers/Api
ls app/Models
ls app/Http/Middleware
ls app/Http/Requests
ls database/migrations
ls database/seeders
ls routes
```

Baca file berikut sampai paham polanya:

- `routes/web.php` dan `routes/api.php`
- Semua file di `app/Http/Controllers` (web dan api)
- Semua file di `app/Models` (perhatikan `$fillable`, relasi, casts)
- Middleware pembatas role/admin di `app/Http/Middleware` dan cara didaftarkan di `bootstrap/app.php`
- Semua migration dan seeder
- Cara login/logout dan cara respons JSON dibentuk di controller API (format key, status code, apakah memakai API Resource)

Kembali ke proyek baru:

```bash
cd ../pinjam-ruang
```

## 4. Pemetaan Konversi (Perpustakaan menjadi Pinjam-Ruang)

| app-perpustakaan | pinjam-ruang | Keterangan |
|---|---|---|
| Category (kategori buku) | RoomCategory (kategori ruang) | Contoh: Ruang Kelas, Laboratorium, Aula, Ruang Rapat |
| Book (buku) | Room (ruang) | Data master ruang yang bisa dipinjam |
| Loan / peminjaman buku | Booking (peminjaman ruang) | Transaksi pengajuan dan persetujuan |
| Role admin / petugas | Role admin / petugas / peminjam | Sesuaikan dengan pola middleware yang ada |
| AuthController | AuthController | Login, logout, (register bila ada di referensi) |
| Controller API pertemuan 09 | Api/RoomController, Api/BookingController | JSON, status code sama pola |

Jika nama kelas di referensi berbeda dari tabel di atas, ikuti nama aslinya lalu sesuaikan konsepnya.

## 5. Spesifikasi Database

Buat migration (urutan sesuai ketergantungan foreign key).

**users** (pakai bawaan Laravel, tambahkan kolom sesuai pola referensi)
- `role` (enum/string: `admin`, `petugas`, `peminjam`, default `peminjam`)
- `nim_nip` (nullable), `no_hp` (nullable), `organisasi` (nullable)

**room_categories**
- `id`, `nama` (unique), `deskripsi` (nullable), timestamps

**rooms**
- `id`, `room_category_id` (FK, cascade/restrict ikuti pola referensi)
- `kode` (unique), `nama`, `kapasitas` (unsigned integer), `lokasi`
- `fasilitas` (text, nullable), `deskripsi` (text, nullable)
- `status` (enum: `tersedia`, `perbaikan`, `nonaktif`, default `tersedia`)
- timestamps

**bookings**
- `id`, `user_id` (FK), `room_id` (FK)
- `tanggal` (date), `jam_mulai` (time), `jam_selesai` (time)
- `keperluan` (string), `jumlah_peserta` (unsigned integer, nullable)
- `status` (enum: `menunggu`, `disetujui`, `ditolak`, `dibatalkan`, `selesai`, default `menunggu`)
- `catatan_admin` (text, nullable), `approved_by` (FK ke users, nullable)
- timestamps

## 6. Model dan Relasi

- `RoomCategory` hasMany `Room`
- `Room` belongsTo `RoomCategory`, hasMany `Booking`
- `Booking` belongsTo `User`, belongsTo `Room`, belongsTo `User` sebagai `approver` (lewat `approved_by`)
- `User` hasMany `Booking`
- Isi `$fillable` dan `casts` (tanggal sebagai date) sesuai pola referensi.

## 7. Controller yang Harus Dibuat

Salin pola dari controller referensi, lalu konversi:

**Web (ikuti pola CRUD referensi)**
- `AuthController`: login, logout (pola pertemuan 08)
- `RoomCategoryController`: CRUD (hanya admin)
- `RoomController`: CRUD (admin dan petugas)
- `BookingController`: ajukan, daftar, detail, batalkan (peminjam); setujui, tolak, tandai selesai (admin dan petugas)
- `DashboardController`: ringkasan angka (total ruang, peminjaman menunggu, peminjaman hari ini)

**API (ikuti pola REST API pertemuan 09, dan autentikasi pertemuan berikutnya bila ada di referensi)**
- `Api/AuthController`: login (dan logout/token bila referensi memakainya)
- `Api/RoomCategoryController`: index, show, store, update, destroy
- `Api/RoomController`: index (dengan filter `kategori`, `status`, `kapasitas_min`, `search`), show, store, update, destroy
- `Api/BookingController`: index, show, store, update status
- `Api/RoomAvailabilityController` (atau method khusus): cek ketersediaan ruang pada tanggal tertentu

Buat juga Form Request atau validasi inline sesuai yang dipakai referensi.

## 8. Aturan Bisnis (Wajib Diimplementasikan di Backend)

1. Peminjam hanya bisa melihat dan membatalkan peminjamannya sendiri.
2. Admin dan petugas bisa melihat semua peminjaman dan mengubah status.
3. `jam_selesai` harus lebih besar dari `jam_mulai`, dan `tanggal` tidak boleh sebelum hari ini.
4. Ruang berstatus `perbaikan` atau `nonaktif` tidak bisa dipinjam.
5. Cegah bentrok jadwal: tolak pengajuan jika ada booking berstatus `menunggu` atau `disetujui` pada ruang dan tanggal yang sama dengan jam yang beririsan. Rumus irisan: `jam_mulai_baru < jam_selesai_lama AND jam_selesai_baru > jam_mulai_lama`.
6. `jumlah_peserta` tidak boleh melebihi `kapasitas` ruang.
7. Booking hanya bisa dibatalkan peminjam saat statusnya `menunggu`.
8. Saat disetujui atau ditolak, isi `approved_by` dan `catatan_admin` (wajib saat ditolak).
9. Ruang tidak boleh dihapus jika masih punya booking aktif (`menunggu`/`disetujui`); begitu juga kategori yang masih punya ruang.

## 9. Routes

- Web: `routes/web.php` dengan middleware auth dan middleware role seperti pola referensi (admin only untuk kategori, admin/petugas untuk kelola ruang dan persetujuan).
- API: `routes/api.php` dengan prefix dan proteksi mengikuti referensi. Endpoint publik: daftar ruang dan ketersediaan. Endpoint lain terproteksi.

Daftar endpoint API minimal:

```
POST   /api/login
GET    /api/room-categories
POST   /api/room-categories
GET    /api/room-categories/{id}
PUT    /api/room-categories/{id}
DELETE /api/room-categories/{id}
GET    /api/rooms
POST   /api/rooms
GET    /api/rooms/{id}
PUT    /api/rooms/{id}
DELETE /api/rooms/{id}
GET    /api/rooms/{id}/availability?tanggal=YYYY-MM-DD
GET    /api/bookings
POST   /api/bookings
GET    /api/bookings/{id}
PATCH  /api/bookings/{id}/status
```

## 10. Seeder

Buat seeder agar bisa langsung diuji:

- 1 admin, 1 petugas, 2 peminjam (password sama, misalnya `password`, dan tuliskan di komentar seeder)
- 4 kategori ruang
- 8 sampai 10 ruang dengan kapasitas bervariasi, 1 di antaranya berstatus `perbaikan`
- 5 booking contoh dengan status berbeda-beda

## 11. Pengujian

1. Jalankan `php artisan migrate:fresh --seed`.
2. Jalankan `php artisan serve`.
3. Buat file `docs/postman-pinjam-ruang.json` (Postman Collection v2.1) berisi semua endpoint di bagian 9, lengkap dengan contoh body JSON dan variabel `{{base_url}}`.
4. Buat 3 skenario uji di dalam collection: (a) pengajuan sukses, (b) pengajuan bentrok jadwal harus ditolak, (c) peminjam mengakses data milik orang lain harus ditolak.

## 12. Definition of Done

- `migrate:fresh --seed` berjalan tanpa error.
- Semua route muncul di `route:list`.
- Semua endpoint API merespons JSON dengan status code yang benar (200, 201, 401, 403, 404, 422).
- Aturan bisnis di bagian 8 berfungsi.
- Tidak ada file frontend baru, dan folder `app-perpustakaan` tidak berubah.
- Di akhir, tampilkan ringkasan: daftar file yang dibuat/diubah, catatan perbedaan dari referensi, dan hal yang masih perlu keputusan saya.

Kerjakan bertahap: (1) migration + model + seeder, (2) controller web + route web, (3) controller API + route API, (4) Postman collection. Berhenti dan lapor setelah tiap tahap selesai.
