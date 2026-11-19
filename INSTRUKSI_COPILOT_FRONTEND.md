# INSTRUKSI COPILOT: Audit Keamanan Backend dan Frontend Blade Sederhana (`pinjam-ruang`)

> Tempel seluruh isi file ini ke Copilot Chat (mode Agent) di folder `pinjam-ruang`.
> Urutan wajib: Tahap 0 (audit dan perbaikan keamanan), lalu Tahap 1 sampai 4 (frontend).
> Frontend dibuat SEDERHANA dan mudah diubah. Fungsi dan keamanan lebih penting daripada tampilan.

---

## 1. Aturan Wajib

1. Jangan mengubah folder `app-perpustakaan` (hanya dibaca untuk melihat pola layout Blade).
2. Jangan merusak API. Setelah semua perubahan, endpoint API harus tetap lolos tes Postman yang sudah ada.
3. Jangan memasang paket frontend baru (tanpa Vue/React/Livewire). Gunakan Blade, satu file CSS biasa, dan JavaScript seperlunya.
4. Ikuti pola layout Blade dari materi (`app-perpustakaan`), termasuk cara `@extends`, `@section`, dan `@yield`.
5. Semua teks tampilan berbahasa Indonesia.
6. Berhenti dan laporkan setelah tiap tahap selesai.

---

## 2. TAHAP 0: Audit Keamanan Backend (kerjakan SEBELUM frontend)

Periksa seluruh kode di `app/`, `routes/`, `database/`, dan `.env.example`. Untuk setiap butir, tulis hasil: AMAN, DIPERBAIKI (jelaskan perubahan), atau PERLU KEPUTUSAN SAYA. Perbaiki yang jelas salah. Jangan mengubah perilaku yang sudah benar.

### 2.1 Autentikasi dan sesi
- [ ] Password disimpan ter-hash (`Hash::make` atau cast `hashed`), tidak pernah teks biasa
- [ ] Login web memanggil `session()->regenerate()` setelah berhasil
- [ ] Logout web memanggil `invalidate()` dan `regenerateToken()`
- [ ] Login web dan API dibatasi percobaan (rate limiting, misalnya `throttle:5,1` atau `RateLimiter`)
- [ ] Pesan gagal login tidak membocorkan apakah email terdaftar atau tidak
- [ ] Token API (jika ada) bisa dicabut saat logout

### 2.2 Otorisasi (siapa boleh apa)
- [ ] Setiap route web dan API terproteksi middleware `auth` sesuai kebutuhan
- [ ] Middleware role terpasang di route kategori (admin saja), ruang dan persetujuan (admin dan petugas)
- [ ] Pengecekan role dilakukan di server (bukan hanya menyembunyikan tombol di tampilan)
- [ ] IDOR dicek: peminjam yang membuka `/bookings/{id}` milik orang lain mendapat 403 atau 404, baik di web maupun API
- [ ] Peminjam tidak bisa mengubah status booking selain membatalkan miliknya sendiri saat status `menunggu`
- [ ] Daftar booking untuk peminjam difilter berdasarkan `auth()->id()` di query, bukan dari input pengguna

### 2.3 Mass assignment dan validasi
- [ ] Tidak ada `Model::create($request->all())` atau `update($request->all())`; gunakan `$request->validated()` atau `only([...])`
- [ ] `$fillable` tiap model tidak memuat `role`, `approved_by`, atau `status` secara berbahaya, atau input tersebut dibatasi di controller
- [ ] Kolom `user_id` pada booking selalu diambil dari pengguna yang login, bukan dari body request
- [ ] Field `role` tidak bisa diisi pengguna saat register atau update profil
- [ ] Semua input divalidasi: tipe, panjang maksimum, `exists:` untuk foreign key, `unique:` untuk kode dan nama, `in:` untuk status
- [ ] Aturan bisnis diverifikasi di server: bentrok jadwal, kapasitas, status ruang, `jam_selesai` setelah `jam_mulai`, tanggal tidak di masa lalu
- [ ] Pengecekan bentrok dilakukan ulang saat booking disetujui, dan idealnya di dalam `DB::transaction` agar dua pengajuan bersamaan tidak lolos keduanya

### 2.4 Injeksi dan output
- [ ] Query memakai Eloquent atau query builder dengan binding; tidak ada `DB::raw` atau `whereRaw` yang menyambung input pengguna langsung
- [ ] Filter pencarian memakai binding (`where('nama','like','%'.$q.'%')` aman, string SQL mentah tidak)
- [ ] Respons API tidak membocorkan field sensitif (`password`, `remember_token`); gunakan `$hidden` atau API Resource

### 2.5 Konfigurasi
- [ ] `.env` ada di `.gitignore` dan tidak pernah ter-commit
- [ ] `.env.example` tidak berisi kredensial asli
- [ ] `APP_DEBUG=false` dan `APP_ENV=production` dicatat sebagai syarat saat nanti di-deploy (untuk lokal tetap boleh true)
- [ ] Seeder akun uji ditandai jelas sebagai data uji dan tidak boleh dipakai di produksi

### 2.6 Integritas data
- [ ] Foreign key terpasang di migration dengan aturan hapus yang masuk akal
- [ ] Ruang dengan booking aktif dan kategori yang masih berisi ruang tidak bisa dihapus
- [ ] Operasi yang mengubah beberapa tabel dibungkus transaksi

**Output Tahap 0:** tabel hasil audit (butir, status, catatan), daftar file yang diubah, dan daftar butir yang perlu keputusan saya. Setelah perbaikan, jalankan `php artisan route:list` dan `php artisan migrate:fresh --seed` untuk memastikan tidak ada error.

---

## 3. TAHAP 1: Pelajari Pola Layout Referensi

```bash
cd ..
cd app-perpustakaan
ls resources/views
ls resources/views/layouts
```

Baca layout utama, partial (navbar, alert), dan satu set view CRUD (index, create, edit). Catat cara pemakaian `@extends`, `@section`, `@yield`, `@include`, `@csrf`, `@method`, `@error`, dan `old()`. Lalu kembali:

```bash
cd ../pinjam-ruang
```

---

## 4. TAHAP 2: Layout dan Autentikasi

Buat struktur berikut (hanya yang belum ada):

```
resources/views/
├── layouts/
│   └── app.blade.php          (kerangka: navbar, area konten, footer)
├── partials/
│   ├── navbar.blade.php       (menu berubah sesuai role)
│   ├── alert.blade.php        (pesan sukses/error dari session)
│   └── pagination-info.blade.php (opsional)
├── auth/
│   └── login.blade.php
├── errors/
│   ├── 403.blade.php
│   └── 404.blade.php
└── dashboard.blade.php
public/css/app.css             (satu file CSS biasa, sederhana)
```

Ketentuan:
- Semua form memakai `@csrf`; form PUT/PATCH/DELETE memakai `@method`.
- Output data memakai `{{ }}` (ter-escape). Dilarang memakai `{!! !!}` untuk data dari pengguna.
- Navbar menampilkan menu sesuai role (lihat tabel di bagian 6), nama pengguna, dan tombol logout (berupa form POST).
- `alert.blade.php` menampilkan `session('success')`, `session('error')`, dan `$errors` secara ringkas.
- CSS sederhana: font bawaan, tabel rapi, tombol berwarna berbeda untuk aksi (simpan, ubah, hapus), badge warna untuk status. Beri komentar di CSS agar mudah diubah.

---

## 5. TAHAP 3: Halaman per Fitur

Sesuaikan controller web seperlunya (kirim variabel ke view, redirect dengan pesan flash, paginasi), tanpa mengubah aturan bisnis.

### 5.1 Kategori ruang (admin saja)
```
resources/views/categories/
├── index.blade.php     (tabel + tombol tambah, ubah, hapus)
├── create.blade.php
├── edit.blade.php
└── _form.blade.php     (form bersama create dan edit)
```

### 5.2 Ruang (admin dan petugas untuk kelola; semua login untuk melihat)
```
resources/views/rooms/
├── index.blade.php     (tabel/kartu sederhana + filter: kategori, status, kapasitas minimal, pencarian + paginasi)
├── show.blade.php      (detail ruang + jadwal terisi per tanggal + tombol "Ajukan Peminjaman")
├── create.blade.php
├── edit.blade.php
└── _form.blade.php
```
`show.blade.php` memuat input tanggal (GET) yang menampilkan daftar booking `menunggu` dan `disetujui` pada tanggal itu.

### 5.3 Peminjaman
```
resources/views/bookings/
├── index.blade.php     (peminjam: miliknya; admin/petugas: semua + filter status)
├── create.blade.php    (pilih ruang, tanggal, jam, keperluan, jumlah peserta)
└── show.blade.php      (detail + tombol aksi sesuai role dan status)
```
Tombol aksi di `show.blade.php`:
- Peminjam pemilik, status `menunggu`: tombol "Batalkan"
- Admin/petugas, status `menunggu`: tombol "Setujui" dan form "Tolak" (kolom catatan wajib)
- Admin/petugas, status `disetujui`: tombol "Tandai Selesai"
- Tombol hanya tampil jika diizinkan, tetapi pengecekan sebenarnya tetap di server (sudah diaudit di Tahap 0)

Badge status: menunggu (kuning), disetujui (hijau), ditolak (merah), dibatalkan (abu-abu), selesai (biru).

### 5.4 Dashboard
`dashboard.blade.php` menampilkan kartu ringkas:
- Admin/petugas: total ruang, peminjaman menunggu, peminjaman hari ini
- Peminjam: jumlah peminjaman saya, peminjaman terdekat

---

## 6. Menu dan Hak Akses Tampilan

| Menu | admin | petugas | peminjam |
|---|:---:|:---:|:---:|
| Dashboard | ✅ | ✅ | ✅ |
| Kategori Ruang | ✅ | ❌ | ❌ |
| Kelola Ruang (tambah/ubah/hapus) | ✅ | ✅ | ❌ |
| Daftar Ruang (lihat) | ✅ | ✅ | ✅ |
| Peminjaman (semua) | ✅ | ✅ | ❌ |
| Peminjaman Saya + Ajukan | ✅ | ✅ | ✅ |

---

## 7. Route Web yang Dipakai

Gunakan route web yang sudah ada dari tahap backend; tambahkan yang kurang dengan middleware yang sama:

```
GET  /login                   POST /login        POST /logout
GET  /dashboard
Resource: /categories         (admin)
Resource: /rooms              (index, show untuk semua login; sisanya admin/petugas)
Resource: /bookings           (index, create, store, show)
PATCH /bookings/{id}/status   (aksi status)
```

---

## 8. TAHAP 4: Pengujian Manual

Jalankan `php artisan migrate:fresh --seed` dan `php artisan serve`, lalu uji di browser untuk tiap akun (password semua `password`):

| Akun | Yang harus bisa | Yang harus ditolak |
|---|---|---|
| admin@pinjamruang.test | Semua menu, CRUD kategori dan ruang, setujui/tolak | - |
| petugas@pinjamruang.test | CRUD ruang, setujui/tolak | Menu Kategori (403) |
| budi@pinjamruang.test | Lihat ruang, ajukan, batalkan miliknya | Kelola ruang (403), buka booking milik Sari (403/404) |

Cek tambahan: validasi form menampilkan pesan error dan mempertahankan isian (`old()`), pengajuan bentrok ditolak dengan pesan jelas, hapus ruang aktif ditolak dengan pesan, logout berfungsi, dan halaman 403/404 tampil.

Setelah selesai, jalankan ulang skenario inti di Postman untuk memastikan API tidak rusak.

---

## 9. Definition of Done

- Tahap 0 selesai, tabel audit tersedia, tidak ada butir kritis yang tersisa tanpa keputusan saya
- Semua halaman di bagian 4 dan 5 berfungsi untuk tiga role
- Semua form memakai `@csrf`, tidak ada `{!! !!}` untuk data pengguna
- Tidak ada paket baru, tidak ada perubahan di `app-perpustakaan`
- API tetap lolos tes Postman
- Laporan akhir: daftar file dibuat/diubah, ringkasan audit, dan hal yang perlu keputusan saya
