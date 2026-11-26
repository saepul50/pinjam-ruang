@extends('layouts.app')

@section('title', 'Cara Booking')

@section('content')
    @include('partials.page-header', [
        'eyebrow' => 'PANDUAN PEMINJAMAN',
        'title' => 'Cara booking ruang',
        'subtitle' => 'Empat langkah mudah dari memilih ruang sampai pengajuan disetujui.',
    ])

    <section class="guide-steps" aria-label="Langkah peminjaman ruang">
        <article class="guide-step">
            <span class="guide-step-number">1</span>
            <h2>Pilih ruang</h2>
            <p>Buka Jelajahi atau Daftar Ruang, lalu pilih ruang yang sesuai kebutuhan.</p>
        </article>
        <article class="guide-step">
            <span class="guide-step-number">2</span>
            <h2>Isi formulir</h2>
            <p>Isi tanggal, jam mulai dan selesai, keperluan, serta jumlah peserta.</p>
        </article>
        <article class="guide-step">
            <span class="guide-step-number">3</span>
            <h2>Kirim pengajuan</h2>
            <p>Periksa kembali data dan kirim pengajuan untuk diperiksa admin.</p>
        </article>
        <article class="guide-step">
            <span class="guide-step-number">4</span>
            <h2>Pantau status</h2>
            <p>Cek hasil pengajuan secara berkala melalui menu Peminjaman Saya.</p>
        </article>
    </section>

    <div class="guide-info-grid">
        <section class="card guide-status-card">
            <h2>Arti status pengajuan</h2>
            <ul class="guide-status-list">
                <li><span class="badge badge-menunggu">Menunggu</span><p>Sedang diperiksa admin. Pengajuan masih bisa dibatalkan.</p></li>
                <li><span class="badge badge-disetujui">Disetujui</span><p>Ruang siap dipakai sesuai jadwal yang diajukan.</p></li>
                <li><span class="badge badge-ditolak">Ditolak</span><p>Jadwal bentrok atau data kurang. Ajukan ulang bila perlu.</p></li>
                <li><span class="badge badge-dibatalkan">Dibatalkan</span><p>Pengajuan dibatalkan oleh peminjam.</p></li>
            </ul>
        </section>

        <section class="card guide-reminder-card">
            <h2>Sebelum mengajukan</h2>
            <ul>
                <li>Siapkan tanggal, jam, keperluan, dan jumlah peserta.</li>
                <li>Pastikan jumlah peserta tidak melebihi kapasitas ruang.</li>
                <li>Pastikan jadwal ruang tidak bentrok dengan pengajuan lain.</li>
                <li>Tunggu persetujuan admin sebelum menggunakan ruang.</li>
            </ul>
        </section>
    </div>

    <section class="guide-cta">
        <div>
            <h2>Sudah siap?</h2>
            <p>Mulai dengan memilih ruang yang tersedia.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('rooms.list') }}">Lihat daftar ruang</a>
    </section>
@endsection
