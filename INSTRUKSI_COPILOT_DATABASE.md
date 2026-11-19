# INSTRUKSI COPILOT: Koneksi Database dan Data Uji Multi-Role (`pinjam-ruang`)

> Tempel seluruh isi file ini ke Copilot Chat (mode Agent) di folder `pinjam-ruang`.
> Jalankan setelah migration, model, dan seeder dasar dari `INSTRUKSI_COPILOT.md` sudah ada.
> Cakupan: hanya konfigurasi database dan data uji. Jangan mengubah frontend dan jangan mengubah folder `app-perpustakaan`.

---

## 1. Tujuan

1. Menyambungkan proyek `pinjam-ruang` ke database sendiri bernama `pinjam_ruang` dengan cara yang sama seperti `app-perpustakaan`.
2. Membuat data uji lengkap untuk tiga role (admin, petugas, peminjam) agar semua endpoint bisa dites di Postman.

## 2. Langkah A: Lihat Cara Koneksi di Proyek Referensi

Jalankan di terminal (posisi awal: di dalam folder `pinjam-ruang`):

```bash
cd ..
cd app-perpustakaan
```

Baca (jangan diubah): `.env` (bagian `DB_*`), `config/database.php`, dan `.env.example`. Catat jenis database yang dipakai (MySQL/MariaDB/SQLite), host, port, username, dan apakah password kosong.

Kembali ke proyek baru:

```bash
cd ../pinjam-ruang
```

## 3. Langkah B: Atur `.env` Proyek Baru

1. Jika belum ada, salin file env:

```bash
cp .env.example .env
php artisan key:generate
```

(Di Windows PowerShell: `copy .env.example .env`)

2. Atur bagian database di `.env` mengikuti jenis database di referensi. Contoh untuk MySQL:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_pinjamruang
DB_USERNAME=root
DB_PASSWORD=
```

Pakai username, password, host, dan port yang SAMA dengan referensi. Hanya `DB_DATABASE` yang berbeda, yaitu `pinjam_ruang`.

3. Jika memakai SQLite, buat file `database/database.sqlite` kosong dan atur `DB_CONNECTION=sqlite` (hapus baris `DB_HOST` dan sejenisnya sesuai pola referensi).

4. Pastikan `SESSION_DRIVER`, `CACHE_STORE`, dan `QUEUE_CONNECTION` sama polanya dengan referensi supaya tidak butuh tabel tambahan yang belum ada.

## 4. Langkah C: Buat Database dan Cek Koneksi

1. Buat database `pinjam_ruang` di MySQL (lewat phpMyAdmin, HeidiSQL, atau perintah di bawah). Jika tidak bisa membuat otomatis, beri tahu saya langkah manualnya.

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS pinjam_ruang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

2. Bersihkan cache konfigurasi dan cek koneksi:

```bash
php artisan config:clear
php artisan migrate:status
```

Jika error koneksi, tampilkan pesan errornya dan perbaiki `.env`, jangan menebak.

## 5. Langkah D: Seeder Data Uji

Perbarui atau buat seeder berikut, lalu daftarkan semuanya di `DatabaseSeeder` dengan urutan: User, RoomCategory, Room, Booking. Gunakan `Hash::make` atau pola yang dipakai referensi untuk password.

### 5.1 Akun uji (password semua: `password`)

| Nama | Email | Role | Keterangan |
|---|---|---|---|
| Admin Sistem | admin@pinjamruang.test | admin | Akses penuh |
| Petugas Ruang | petugas@pinjamruang.test | petugas | Kelola ruang dan persetujuan |
| Budi Santoso | budi@pinjamruang.test | peminjam | NIM/organisasi diisi |
| Sari Wulandari | sari@pinjamruang.test | peminjam | NIM/organisasi diisi |

Tulis tabel akun ini sebagai komentar di bagian atas `UserSeeder`.

### 5.2 Kategori ruang

Ruang Kelas, Laboratorium, Aula, Ruang Rapat.

### 5.3 Data ruang (10 ruang)

| Kode | Nama | Kategori | Kapasitas | Lokasi | Status |
|---|---|---|---|---|---|
| RK-101 | Ruang Kelas 101 | Ruang Kelas | 40 | Gedung A Lt.1 | tersedia |
| RK-102 | Ruang Kelas 102 | Ruang Kelas | 40 | Gedung A Lt.1 | tersedia |
| RK-201 | Ruang Kelas 201 | Ruang Kelas | 30 | Gedung A Lt.2 | tersedia |
| LAB-01 | Lab Komputer 1 | Laboratorium | 35 | Gedung B Lt.1 | tersedia |
| LAB-02 | Lab Jaringan | Laboratorium | 25 | Gedung B Lt.2 | tersedia |
| LAB-03 | Lab Multimedia | Laboratorium | 25 | Gedung B Lt.2 | perbaikan |
| AUL-01 | Aula Utama | Aula | 200 | Gedung C | tersedia |
| AUL-02 | Aula Mini | Aula | 80 | Gedung C | tersedia |
| RR-01 | Ruang Rapat Dosen | Ruang Rapat | 15 | Gedung A Lt.3 | tersedia |
| RR-02 | Ruang Rapat Organisasi | Ruang Rapat | 20 | Gedung D | nonaktif |

Isi kolom `fasilitas` dengan teks singkat (contoh: "Proyektor, AC, Whiteboard").

### 5.4 Data peminjaman (10 data)

Gunakan Carbon relatif terhadap hari ini (`now()->addDays(n)`), BUKAN tanggal tetap, supaya data tidak menjadi masa lalu.

| # | Peminjam | Ruang | Tanggal | Jam | Status | Catatan |
|---|---|---|---|---|---|---|
| 1 | Budi | RK-101 | besok | 08:00-10:00 | disetujui | Dipakai untuk uji bentrok |
| 2 | Budi | LAB-01 | besok | 13:00-15:00 | menunggu | |
| 3 | Sari | AUL-01 | +3 hari | 09:00-12:00 | menunggu | Rapat organisasi |
| 4 | Sari | RR-01 | +2 hari | 10:00-11:00 | disetujui | |
| 5 | Budi | AUL-02 | +5 hari | 14:00-16:00 | ditolak | catatan_admin wajib terisi |
| 6 | Sari | RK-102 | kemarin | 08:00-10:00 | selesai | |
| 7 | Budi | LAB-02 | +1 hari | 10:00-12:00 | dibatalkan | |
| 8 | Sari | RK-201 | +4 hari | 08:00-09:30 | menunggu | |
| 9 | Budi | RK-102 | +6 hari | 13:00-15:00 | disetujui | |
| 10 | Sari | LAB-01 | +2 hari | 08:00-10:00 | disetujui | |

Untuk status `disetujui` dan `ditolak`, isi `approved_by` dengan id petugas (atau admin) dan `catatan_admin` yang masuk akal. Pastikan tidak ada dua booking `menunggu`/`disetujui` yang bentrok satu sama lain.

## 6. Langkah E: Jalankan dan Verifikasi

```bash
php artisan migrate:fresh --seed
php artisan tinker --execute="echo App\Models\User::count().' user, '.App\Models\Room::count().' ruang, '.App\Models\Booking::count().' booking';"
```

Hasil yang diharapkan: 4 user, 10 ruang, 10 booking.

Lalu cek tiap role bisa login (sesuai mekanisme referensi) dan jalankan:

```bash
php artisan route:list
php artisan serve
```

## 7. Larangan

- Jangan mengubah `app-perpustakaan` dan jangan menyalin file `.env` referensi mentah-mentah.
- Jangan commit `.env` ke Git (pastikan ada di `.gitignore`).
- Jangan membuat file frontend.

## 8. Laporan Akhir

Setelah selesai, tampilkan: jenis database yang dipakai, isi bagian `DB_*` (password disamarkan), daftar file seeder yang dibuat atau diubah, jumlah data per tabel, dan masalah yang belum terselesaikan.
