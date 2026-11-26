# INSTRUKSI COPILOT: Penyesuaian Data Ruang (Lantai) dan Denah 2x2 (`pinjam-ruang`)

> Tempel isi file ini ke Copilot Chat (mode Agent) di folder `pinjam-ruang`.
> Dikerjakan setelah tema (`INSTRUKSI_COPILOT_TEMA.md`) diterapkan. Jangan mengubah folder `app-perpustakaan`.

---

## 1. Tujuan Perubahan

1. Setiap ruang WAJIB punya lantai.
2. Hapus ukuran ruang (`luas_m2` / meter persegi) dari seluruh sistem. Data yang ditampilkan per ruang cukup: **fasilitas, kapasitas, dan lokasi (lantai berapa)**.
3. Denah dibuat dengan **grid 2x2 (4 slot per lantai)** agar cukup untuk semua ruang, karena satu lantai maksimal berisi 4 ruang.
4. Pin lokasi di denah harus **tepat di tengah ruangan** (tengah secara horizontal dan vertikal).

## 2. Aturan Wajib

1. Tambahkan migration BARU. Jangan mengedit migration lama yang sudah dijalankan.
2. Jangan memasang paket baru. Jangan mengubah `app-perpustakaan`.
3. Semua form tetap `@csrf`; data pengguna dirender dengan `{{ }}`, dilarang `{!! !!}`.
4. Hak akses dan aturan bisnis lain (bentrok jadwal, kapasitas, role) tidak boleh berubah.
5. Perubahan pada API wajib diikuti pembaruan dokumen uji Postman (lihat bagian 9).
6. Berhenti dan laporkan setelah tiap tahap.

## 3. Tahap 0: Periksa Kondisi Saat Ini

Sebelum mengubah apa pun, laporkan:
- Kolom tabel `rooms` saat ini (`php artisan tinker --execute="print_r(Illuminate\Support\Facades\Schema::getColumnListing('rooms'));"`)
- Migration yang sudah menambah `gedung`, `lantai`, `luas_m2`, `foto_url`
- File yang memakai `luas_m2` (`grep -rn "luas" app resources database routes public docs`)
- View denah yang ada dan kelas CSS yang dipakai (`.floorplan`, `.room`, `.roompin`)

## 4. Tahap 1: Skema Database

Buat migration baru, misalnya `adjust_rooms_for_floor_plan`, dengan isi:

1. Isi dulu baris lama yang `lantai`-nya kosong: `DB::table('rooms')->whereNull('lantai')->update(['lantai' => 1]);`
2. Ubah `lantai` menjadi **NOT NULL**, unsigned tinyInteger, default 1.
3. Ubah `gedung` menjadi NOT NULL dengan default `'Gedung A'` (isi baris lama yang kosong dengan nilai ini lebih dulu).
4. Hapus kolom `luas_m2` (`dropColumn`).
5. Tambah kolom `posisi_denah` (unsigned tinyInteger, nullable): nomor slot di denah, 1 sampai 4.
6. Tambah unique index gabungan `(gedung, lantai, posisi_denah)` agar dua ruang tidak menempati slot yang sama (nilai null tidak bentrok).
7. Implementasikan `down()` yang mengembalikan perubahan.

Arti slot denah:

```
+-----------+-----------+
|  slot 1   |  slot 2   |
|  kiri atas|  kanan atas|
+-----------+-----------+
|  slot 3   |  slot 4   |
|  kiri bawah|kanan bawah|
+-----------+-----------+
```

Makna kolom setelah perubahan:

| Kolom | Isi |
|---|---|
| `gedung` | Nama gedung, contoh "Gedung A" |
| `lantai` | Angka 1 sampai 20, wajib |
| `lokasi` | Keterangan letak singkat di lantai itu (contoh "Sayap kiri, dekat tangga"), tanpa mengulang nomor lantai |
| `kapasitas` | Jumlah orang |
| `fasilitas` | Daftar dipisah koma |
| `posisi_denah` | Slot 1 sampai 4, boleh kosong (diisi otomatis) |

## 5. Tahap 2: Model, Validasi, dan Aturan Slot

1. Model `Room`: hapus `luas_m2` dari `$fillable`; tambahkan `gedung`, `lantai`, `posisi_denah`. Tambahkan accessor `lokasi_lengkap` yang menghasilkan teks "Lantai {lantai} · {gedung} · {lokasi}".
2. Validasi (web dan API, baik Form Request maupun inline):
   - `gedung`: required, string, max 100
   - `lantai`: required, integer, min 1, max 20
   - `lokasi`: nullable, string, max 150
   - `posisi_denah`: nullable, integer, between 1 dan 4
   - `foto_url`: nullable, `url:http,https`, max 255
   - Hapus semua aturan `luas_m2`
3. Aturan maksimal 4 ruang per lantai (di controller web dan API, dalam `DB::transaction` saat menyimpan):
   - Hitung ruang dengan `gedung` dan `lantai` yang sama (kecualikan ruang yang sedang diubah). Jika sudah 4, tolak dengan 422 dan pesan "Lantai ini sudah penuh (maksimal 4 ruang)".
   - Jika `posisi_denah` diisi dan sudah dipakai ruang lain di gedung dan lantai yang sama, tolak 422 dengan pesan "Slot denah sudah terpakai".
   - Jika `posisi_denah` kosong, isi otomatis dengan slot terkecil yang masih kosong (1 sampai 4).
   - Saat ruang dipindah ke lantai atau gedung lain, terapkan aturan yang sama.

## 6. Tahap 3: Seeder

Perbarui `RoomSeeder` (hapus `luas_m2`, isi `gedung`, `lantai`, `lokasi`, `posisi_denah`). Tidak ada lantai yang berisi lebih dari 4 ruang.

| Kode | Gedung | Lantai | Slot | Lokasi (keterangan) | Fasilitas |
|---|---|---|---|---|---|
| RK-101 | Gedung A | 1 | 1 | Sayap kiri | Proyektor, AC, Whiteboard |
| RK-102 | Gedung A | 1 | 2 | Sayap kanan | Proyektor, AC, Speaker |
| RK-201 | Gedung A | 2 | 1 | Dekat tangga | Proyektor, AC |
| RR-01 | Gedung A | 3 | 1 | Ujung koridor | Video Conference, Wi-Fi, AC |
| LAB-01 | Gedung B | 1 | 1 | Sayap kiri | 35 Komputer, LAN, Proyektor |
| LAB-02 | Gedung B | 2 | 1 | Sayap kiri | Perangkat jaringan, LAN, AC |
| LAB-03 | Gedung B | 2 | 2 | Sayap kanan | Kamera, Lighting, Audio |
| AUL-01 | Gedung C | 1 | 1 | Area utama | Sound System, Proyektor, AC |
| AUL-02 | Gedung C | 2 | 1 | Area utama | Sound System, Proyektor, AC |
| RR-02 | Gedung D | 1 | 1 | Sayap kiri | Wi-Fi, Smart TV, Whiteboard |

Status dan kapasitas ruang tidak berubah dari seeder sebelumnya (LAB-03 `perbaikan`, RR-02 `nonaktif`). Kolom lain (kategori, kapasitas, deskripsi) tetap. Jumlah ruang tetap 10.

## 7. Tahap 4: Form dan Tampilan Data

Sesuaikan semua view yang menampilkan ruang:

- `rooms/_form`: hapus input luas; tambahkan `gedung` (teks), `lantai` (angka 1 sampai 20, wajib), `lokasi` (teks), `posisi_denah` (pilihan "Otomatis", 1, 2, 3, 4).
- `rooms/index` dan partial `room-card`: tampilkan nama, kode, "Kapasitas N orang", "Lantai X", dan badge status. Tidak ada luas.
- `rooms/show` (panel detail): tiga kartu angka yaitu **Kapasitas**, **Lantai**, dan **Status**; di bawahnya chip fasilitas (pecah teks dengan `explode(',')`, `trim`, render `{{ }}`) dan teks lokasi lengkap.
- Hapus semua tampilan "Luas ruang" atau "m²".

## 8. Tahap 5: Denah 2x2 dengan Pin di Tengah

Denah ada di halaman daftar ruang (`rooms/index`), di atas daftar kartu. Kontrol: pilih **gedung** dan tombol **lantai** (hanya lantai yang punya ruang pada gedung terpilih). Render di server (Blade), tanpa bergantung JavaScript.

### 8.1 Perilaku
- Query string: `?gedung=Gedung A&lantai=1&ruang={id}`. Default: gedung pertama, lantai terendah, ruang pertama yang terisi.
- Tombol lantai berupa tautan; pin berupa tautan ke `?gedung=..&lantai=..&ruang={id}`. Panel detail di sisi kanan menampilkan ruang terpilih (nama, lokasi lengkap, kapasitas, lantai, fasilitas, status, tombol "Ajukan Peminjaman").
- Ruang berstatus `perbaikan` atau `nonaktif` tetap tampil di denah dengan warna redup dan badge status, dan tombol ajukan nonaktif.
- Slot tanpa ruang tampil sebagai kotak putus-putus berlabel "Kosong", tidak bisa diklik.
- Selalu tampil 4 slot (2x2), apa pun jumlah ruang di lantai itu.

### 8.2 Struktur HTML (acuan)

```html
<div class="mapcanvas">
  <div class="floorplan">
    <!-- selalu 4 sel, urut slot 1..4 -->
    <div class="room-cell selected">
      <span class="room-name">{{ $room->nama }}</span>
      <a class="roompin" href="..." aria-label="Pilih {{ $room->nama }}"><span>{{ $room->kode }}</span></a>
    </div>
    <div class="room-cell room-empty"><span>Kosong</span></div>
    ...
  </div>
  <div class="maplabel">DENAH ILUSTRASI · {{ $gedung }} · LANTAI {{ $lantai }}</div>
</div>
```

### 8.3 CSS (ganti gaya denah lama; hapus garis silang `:before` dan `:after`, dan posisi `left/top` persen)

```css
/* ===== DENAH ===== */
.floorplan{
  position:absolute; inset:7% 8%;
  display:grid;
  grid-template-columns:1fr 1fr;
  grid-template-rows:1fr 1fr;
  gap:12px; padding:12px;            /* gap = lorong */
  background:#dfe6f0;                /* warna lorong */
  border:7px solid #d8e0ea; border-radius:8px;
  box-shadow:0 5px 18px #233b6014;
}
.room-cell{
  position:relative;
  display:grid; place-items:center;  /* pin tepat di tengah sel */
  background:#f7f9fc; border:1px solid #cbd6e4; border-radius:8px;
  min-width:0; min-height:0;
}
.room-cell.selected{background:var(--blue-soft); border:2px solid var(--blue)}
.room-cell.is-unavailable{opacity:.6}
.room-cell.room-empty{background:transparent; border:2px dashed #b9c5d6; color:#9aa7ba; font-size:11px; font-weight:700}
.room-name{
  position:absolute; top:8px; left:10px; right:10px;
  font-size:11px; font-weight:700; line-height:1.3; color:#75839a;
  overflow:hidden; text-overflow:ellipsis; white-space:nowrap; text-align:left;
}
.room-cell.selected .room-name{color:var(--blue-dark)}
.roompin{
  width:38px; height:38px; border-radius:50%;      /* lingkaran agar pusat visual = pusat kotak */
  display:grid; place-items:center;
  background:var(--blue); color:#fff; border:3px solid #fff;
  box-shadow:0 4px 12px #1d397044;
  font-size:10px; font-weight:800; text-decoration:none;
  transition:transform .15s, background .15s;
}
.roompin:hover,.room-cell.selected .roompin{background:var(--green); transform:scale(1.12)}
```

Catatan penting: pin lama berbentuk tetes air yang diputar `rotate(-45deg)` dengan posisi `calc(%)`. Karena itu pusat visualnya bergeser dari tengah ruangan. Ganti menjadi lingkaran di dalam sel grid dengan `place-items:center` (tidak ada posisi absolut untuk pin) agar selalu tepat di tengah di semua ukuran layar.

Responsif:
- `max-width:900px`: tinggi `.mapcanvas` 380px.
- `max-width:600px`: tinggi `.mapcanvas` 340px, `.floorplan` `inset:8% 4%`, `gap:8px`, `padding:8px`, `.room-name` 9px, `.roompin` 30px dan font 9px.

## 9. Tahap 6: API dan Dokumen Uji Postman

API:
- `GET /api/rooms` dan `GET /api/rooms/{id}`: respons memuat `gedung`, `lantai`, `lokasi`, `posisi_denah`; field `luas_m2` DIHAPUS.
- `POST /api/rooms` dan `PUT /api/rooms/{id}`: `lantai` wajib; terapkan aturan maksimal 4 ruang per lantai dan slot unik (bagian 5).
- Tambahkan filter baru di `GET /api/rooms`: `?gedung=Gedung A` dan `?lantai=2`. Filter lama tetap berfungsi.

Perbarui dokumen uji Postman (`docs/postman-pinjam-ruang.json` bila ada, dan sampaikan perubahannya kepada saya sebagai daftar):
- Body `POST /rooms` sekarang harus memuat `gedung` dan `lantai`. Contoh baru:

```json
{
  "room_category_id": 1,
  "kode": "RK-301",
  "nama": "Ruang Kelas 301",
  "kapasitas": 35,
  "gedung": "Gedung A",
  "lantai": 3,
  "lokasi": "Sayap kanan",
  "fasilitas": "Proyektor, AC",
  "deskripsi": "Ruang kelas lantai 3",
  "status": "tersedia"
}
```

- Skenario baru: tanpa `lantai` mengembalikan 422; lantai 0 atau 25 mengembalikan 422; menambah ruang ke-5 pada satu gedung dan lantai yang sudah berisi 4 ruang mengembalikan 422; memakai `posisi_denah` yang sudah terpakai mengembalikan 422; `GET /rooms?lantai=2` mengembalikan 4 ruang dari seed (RK-201, LAB-02, LAB-03, AUL-02).

## 10. Pengujian

1. `php artisan migrate:fresh --seed` tanpa error; 10 ruang, tidak ada kolom `luas_m2` (cek `Schema::getColumnListing('rooms')`).
2. `grep -rn "luas\|m²" app resources database routes public` tidak menemukan sisa pemakaian.
3. Buka `rooms/index` untuk tiap gedung dan lantai: denah selalu 2x2, slot kosong tampil putus-putus, klik pin mengganti panel detail.
4. Cek pin di tengah: buka DevTools Console di halaman denah dan jalankan:

```js
document.querySelectorAll('.room-cell .roompin').forEach(p=>{
  const c=p.closest('.room-cell').getBoundingClientRect(), r=p.getBoundingClientRect();
  console.log(p.textContent.trim(),
    Math.abs((c.left+c.width/2)-(r.left+r.width/2))<1.5,
    Math.abs((c.top+c.height/2)-(r.top+r.height/2))<1.5);
});
```

   Semua baris harus mencetak `true true` pada lebar 1280px, 900px, dan 600px.
5. Coba tambah ruang ke-5 di Gedung A lantai 1 lewat form web dan API: harus ditolak dengan pesan jelas.
6. Jalankan ulang skenario inti Postman (login, ruang, booking, bentrok, status) dan skenario baru di bagian 9.

## 11. Definition of Done

- Setiap ruang punya lantai; kolom `luas_m2` hilang dari database, model, form, tampilan, seeder, dan API
- Data ruang ditampilkan sebagai fasilitas, kapasitas, dan lokasi (lantai)
- Denah 2x2 menampung seluruh ruang per lantai, slot kosong tampil jelas
- Pin lokasi tepat di tengah ruangan di semua ukuran layar (lolos skrip DevTools)
- Aturan maksimal 4 ruang per lantai dan slot unik berfungsi di web dan API
- Dokumen uji Postman diperbarui; tes lama dan baru lolos
- Tidak ada paket baru, tidak ada perubahan di `app-perpustakaan`
- Laporan akhir: daftar file dibuat/diubah, ringkasan migration, dan hal yang perlu keputusan saya
