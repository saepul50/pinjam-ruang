# INSTRUKSI COPILOT: Penyeragaman Tema Tampilan `pinjam-ruang` (berdasarkan Prototype)

> Tempel isi file ini ke Copilot Chat (mode Agent) di folder `pinjam-ruang`.
> Lampirkan juga file `pinjamruang-prototype.html` (atau salin ke `docs/design/pinjamruang-prototype.html` lebih dulu).
> Cakupan: tampilan (Blade, CSS, JS ringan). Fungsi dan keamanan yang sudah berjalan tidak boleh berubah.

---

## 1. Tujuan

Semua halaman Blade (login, dashboard, ruang, peminjaman, kategori, error) memakai SATU tema yang sama, mengikuti gaya `pinjamruang-prototype.html`: bersih, biru, kartu putih dengan sudut membulat, tipografi Manrope dan DM Sans.

Prototype adalah ACUAN VISUAL, bukan kode untuk disalin utuh. Datanya statis (hardcode) dan memakai `innerHTML`. Di proyek ini data harus berasal dari controller dan dirender lewat Blade.

## 2. Aturan Wajib

1. Jangan mengubah aturan bisnis, route, atau respons API. Tes Postman yang sudah ada harus tetap lolos.
2. Jangan memasang paket baru (tanpa Tailwind, Bootstrap, Vue, Livewire). Cukup Blade, satu file CSS, dan satu file JS kecil.
3. Semua form tetap memakai `@csrf` dan `@method`. Data pengguna dirender dengan `{{ }}`, dilarang `{!! !!}`.
4. Jika JavaScript membuat HTML dari data, dilarang memakai `innerHTML` dengan data pengguna. Pakai `textContent` atau `data-*` attribute, atau render di sisi server.
5. Ikuti pola layout Blade dari `app-perpustakaan` (`@extends`, `@section`, `@include`), jadi partial pakai `@include`, bukan komponen kelas.
6. Jangan menulis gaya `style="..."` inline berulang. Semua gaya masuk `public/css/app.css`.
7. Teks tampilan berbahasa Indonesia.
8. Berhenti dan laporkan setelah tiap tahap.

## 3. Design Tokens (wajib dipakai sebagai CSS variable)

Letakkan di bagian atas `public/css/app.css`:

```css
:root {
  --ink: #17233b;      /* teks utama */
  --muted: #7b879b;    /* teks sekunder */
  --line: #e8edf4;     /* garis/border */
  --blue: #4169e1;     /* warna utama */
  --blue-dark: #3157ce;
  --blue-soft: #edf2ff;
  --green: #15966b;
  --bg: #f6f8fc;       /* latar halaman */
  --white: #fff;

  /* status booking */
  --st-menunggu-bg: #fff6e0;   --st-menunggu-fg: #a66b00;
  --st-disetujui-bg: #e6fff5;  --st-disetujui-fg: #0c8059;
  --st-ditolak-bg: #ffecec;    --st-ditolak-fg: #c0392b;
  --st-dibatalkan-bg: #eef1f6; --st-dibatalkan-fg: #65728a;
  --st-selesai-bg: #edf2ff;    --st-selesai-fg: #3157ce;

  --radius-sm: 10px;
  --radius-md: 14px;
  --radius-lg: 18px;
  --shadow-card: 0 8px 24px #203b6808;
  --shadow-float: 0 8px 24px #203b6810;
}
```

Tipografi:
- Judul dan merek: **Manrope** (700 dan 800)
- Isi teks, form, tabel: **DM Sans** (400, 500, 600, 700)
- Muat dari Google Fonts di `layouts/app.blade.php` (`preconnect` plus `link`), seperti di prototype.

Ukuran acuan: judul halaman `clamp(27px, 3vw, 38px)` weight 800; judul section 19px weight 800; teks isi 14 sampai 15px; label kecil 11 sampai 12px.

## 4. Kerangka Layout Global

`resources/views/layouts/app.blade.php` untuk halaman setelah login, dan `layouts/guest.blade.php` untuk halaman login.

Struktur halaman (mengikuti prototype):

1. **Topbar sticky** (tinggi 76px, putih, garis bawah `--line`): merek di kiri (kotak biru `⌂` + teks `pinjam` + `ruang.` berwarna biru), menu di tengah, profil di kanan.
2. **Menu** berubah sesuai role (aturan hak akses tetap seperti sebelumnya). Menu aktif berwarna biru dan tebal. Pada layar kecil (maks 600px) menu berubah jadi tombol hamburger sederhana atau menu bawah.
3. **Profil**: avatar bulat berisi inisial nama, teks "Halo, {nama depan}", dan menu kecil berisi Logout (pakai `<details><summary>` agar tanpa JS, tombol logout tetap form POST ber-`@csrf`).
4. **Konten**: `<main>` maksimal lebar 1320px, rata tengah, padding 42px 28px.
5. **Header halaman** di setiap halaman: label kecil huruf kapital berwarna biru (eyebrow), judul besar, deskripsi singkat abu-abu.
6. **Flash message** (`partials/alert.blade.php`): tampil sebagai toast gelap di bawah tengah (seperti `.toast` di prototype), hilang otomatis setelah 3 detik oleh `public/js/app.js`. Pesan error validasi tampil sebagai kotak merah lembut di atas form.

Partial yang dibuat (`resources/views/partials/`):

| Partial | Fungsi |
|---|---|
| `navbar.blade.php` | Topbar, menu per role, profil |
| `page-header.blade.php` | Eyebrow, judul, deskripsi (variabel: `$eyebrow`, `$title`, `$subtitle`) |
| `alert.blade.php` | Toast sukses/error dan daftar error validasi |
| `status-badge.blade.php` | Badge status booking dan ruang (variabel: `$status`) |
| `room-card.blade.php` | Kartu ruang seperti `.roomtile` (variabel: `$room`) |
| `stat-card.blade.php` | Kartu angka ringkas seperti `.stat` (variabel: `$label`, `$value`) |
| `empty-state.blade.php` | Tampilan saat data kosong (ikon, teks, tombol aksi) |
| `confirm-delete.blade.php` | Form hapus dengan `confirm()` JS |

## 5. Kelas Komponen CSS (satu gaya untuk seluruh halaman)

Buat kelas berikut di `public/css/app.css`, dikelompokkan dengan komentar `/* ===== NAMA BAGIAN ===== */`:

| Kelas | Gaya mengikuti prototype |
|---|---|
| `.topbar`, `.brand`, `.brandmark`, `.nav`, `.profile`, `.avatar` | Topbar |
| `.container` | `<main>` ber-padding dan lebar maks |
| `.eyebrow`, `.page-title`, `.page-intro` | Header halaman |
| `.card` | Putih, border `--line`, radius 18px, shadow kartu |
| `.btn`, `.btn-primary`, `.btn-secondary`, `.btn-danger`, `.btn-sm` | Tombol primer biru dengan shadow biru lembut; sekunder abu-abu muda; danger merah lembut |
| `.field`, `.field label`, `.input`, `.select`, `.textarea` | Form: label kecil huruf kapital abu-abu, input dengan border `--line`, fokus berwarna biru |
| `.searchbar`, `.searchfield` | Bar pencarian/filter ber-kartu, field dipisah garis tipis |
| `.stats`, `.stat` | Grid kartu angka |
| `.chips`, `.chip` | Label fasilitas |
| `.badge`, `.badge-menunggu`, `.badge-disetujui`, `.badge-ditolak`, `.badge-dibatalkan`, `.badge-selesai`, `.badge-tersedia`, `.badge-perbaikan`, `.badge-nonaktif` | Badge status berbentuk pil |
| `.table-wrap`, `.table` | Tabel di dalam kartu: header huruf kecil abu-abu, baris dengan garis tipis, hover lembut |
| `.rooms-grid`, `.roomtile` | Grid kartu ruang 3 kolom (2 pada 900px, 1 sampai 2 pada 600px) |
| `.workspace` | Layout dua kolom (utama dan panel detail), jadi satu kolom pada 900px |
| `.detailcard`, `.detailimage`, `.detailbody` | Panel detail ruang |
| `.modal-overlay`, `.modal` | Modal ringkasan ruang |
| `.toast` | Notifikasi |
| `.pagination` | Gaya link paginasi Laravel agar senada |
| `.empty` | Empty state |

Breakpoint wajib: `max-width: 900px` dan `max-width: 600px` seperti prototype.

## 6. Pemetaan Halaman ke Gaya Prototype

| Halaman | Tampilan yang diharapkan |
|---|---|
| `auth/login` | Layout `guest`: kartu login di tengah dengan merek di atas, latar `--bg`, tombol primer lebar penuh |
| `dashboard` | Header halaman + grid `.stat` (kartu angka) + kartu "Peminjaman terdekat/terbaru" berisi tabel ringkas |
| `rooms/index` | Header halaman, **searchbar** (kategori, status, kapasitas minimal, kata kunci, tombol "Cari Ruangan →"), lalu `.rooms-grid` berisi `room-card`, plus paginasi. Tombol "Tambah Ruang" hanya untuk admin/petugas |
| `rooms/show` | `.workspace`: kiri kartu jadwal terisi per tanggal (input tanggal GET + daftar booking), kanan `.detailcard` (gambar/placeholder, badge status, nama, lokasi, 3 `.stat`, `.chips` fasilitas, tombol "Ajukan Peminjaman →") |
| `rooms/create`, `rooms/edit` | Kartu form dua kolom (satu kolom di layar kecil) memakai kelas `.field` |
| `bookings/index` | Searchbar filter status/tanggal + tabel dalam `.card` dengan `status-badge` dan tombol "Detail" |
| `bookings/create` | Kartu form: pilih ruang, tanggal, jam, keperluan, jumlah peserta; ringkasan ruang terpilih di sisi kanan jika memungkinkan |
| `bookings/show` | Dua kolom: kiri detail peminjaman, kanan aksi sesuai role dan status (Setujui, Tolak dengan catatan, Batalkan, Tandai Selesai), badge status di bagian atas |
| `categories/*` | Tabel dalam `.card` dan form senada |
| `errors/403`, `errors/404` | Kartu tengah dengan kode besar, pesan, dan tombol kembali |

## 7. Kebutuhan Data Tambahan (agar tampilan sama dengan prototype)

Prototype menampilkan gedung, lantai, luas, foto, dan fasilitas. Skema ruang saat ini hanya punya `lokasi` dan `fasilitas`. Lakukan perubahan KOMPATIBEL-MUNDUR berikut:

1. Buat migration baru yang menambah kolom nullable ke tabel `rooms`:
   - `gedung` (string, nullable)
   - `lantai` (unsigned tinyInteger, nullable)
   - `luas_m2` (unsigned smallInteger, nullable)
   - `foto_url` (string, nullable)
2. Tambahkan ke `$fillable` model `Room`, form request/validasi, form create/edit, dan `RoomSeeder` (isi data contoh: gedung "Gedung Fakultas A", lantai 1 sampai 3, luas dan fasilitas sesuai ruang).
3. Validasi: `gedung` string maks 100, `lantai` integer 1 sampai 20, `luas_m2` integer min 1, `foto_url` nullable dan `url:http,https` maks 255 (hanya http/https agar tidak menerima skema berbahaya).
4. `fasilitas` tetap satu kolom teks berisi daftar dipisah koma. Tampilan memecahnya menjadi `.chip` memakai `explode(',', ...)` dan `trim`, dirender dengan `{{ }}`.
5. Jika `foto_url` kosong atau gagal dimuat, tampilkan placeholder berupa blok gradasi biru lembut dengan ikon `⌂` (CSS saja, tanpa gambar eksternal).
6. Jangan menautkan foto dari Unsplash secara langsung di kode. Foto di seeder boleh dikosongkan agar memakai placeholder.
7. Kolom baru hanya ditambahkan ke respons API, tidak boleh menghapus atau mengganti nama field yang sudah ada. Skenario Postman yang ada tetap harus lolos.

## 8. JavaScript (`public/js/app.js`, maksimal sekitar 60 baris)

Hanya untuk:
- Menyembunyikan toast otomatis setelah 3 detik
- Konfirmasi hapus (`confirm('Yakin menghapus data ini?')`)
- Membuka dan menutup modal ringkasan ruang (isi diambil dari atribut `data-*` yang di-escape Blade, diisi dengan `textContent`)
- Mengubah menu mobile (jika dipakai)

## 9. Tahapan Kerja

1. **Tahap 1: Fondasi.** Tulis ulang `public/css/app.css` (tokens, tipografi, komponen), `layouts/app`, `layouts/guest`, semua partial di bagian 4, dan `public/js/app.js`. Perbarui halaman login dan error. Laporkan.
2. **Tahap 2: Data tambahan.** Migration, model, validasi, form, seeder (bagian 7). Jalankan `php artisan migrate:fresh --seed` dan tes Postman skenario ruang. Laporkan.
3. **Tahap 3: Halaman ruang dan dashboard.** `dashboard`, `rooms/index`, `rooms/show`, `rooms/create`, `rooms/edit`. Laporkan.
4. **Tahap 4: Peminjaman dan kategori.** `bookings/*`, `categories/*`. Laporkan.
5. **Tahap 5 (opsional, kerjakan hanya jika saya minta): Denah.** Bagian "Jelajahi berdasarkan denah" dengan pin ruang dan pemilih lantai, berbasis kolom `gedung` dan `lantai`. Denah bersifat ilustrasi (posisi pin dihitung otomatis dari urutan ruang), tidak perlu tabel koordinat.

## 10. Pengujian

1. Buka setiap halaman dengan akun `admin@pinjamruang.test`, `petugas@pinjamruang.test`, dan `budi@pinjamruang.test` (password `password`).
2. Cek di lebar 1280px, 900px, dan 600px: tidak ada elemen terpotong, tabel bisa digulir horizontal di layar kecil, menu tetap bisa dipakai.
3. Cek konsistensi: font, warna tombol, bentuk kartu, badge status, dan jarak sama di semua halaman. Tidak ada halaman bergaya lama.
4. Cek keamanan tampilan: tidak ada `{!! !!}` untuk data pengguna, semua form punya `@csrf`, tidak ada `innerHTML` dengan data pengguna (`grep -rn "innerHTML" public/js resources/views`).
5. Jalankan ulang skenario inti Postman: login, daftar ruang, pengajuan booking, penolakan bentrok, ubah status. Semua harus sama seperti sebelumnya.

## 11. Definition of Done

- Semua halaman memakai layout, tokens, dan komponen yang sama
- Tampilan sebanding dengan prototype (topbar, searchbar, kartu ruang, panel detail, badge, toast)
- Kolom tambahan ruang berfungsi di form, seeder, dan tampilan
- Fungsi dan hak akses tidak berubah, API lolos tes Postman
- Tidak ada paket baru dan tidak ada perubahan di `app-perpustakaan`
- Laporan akhir: daftar file dibuat/diubah, tangkapan masalah yang ditemukan, dan hal yang perlu keputusan saya
