@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if (auth()->user()->role === 'peminjam')
        <div class="home-page">
            <section class="home-hero">
                <div class="home-copy">
                    <span class="eyebrow">SISTEM PEMINJAMAN RUANG KAMPUS</span>
                    <h1>Pinjam ruang kampus jadi lebih mudah.</h1>
                    <p>Cek ruang yang tersedia, ajukan peminjaman secara online, dan pantau statusnya tanpa perlu datang ke bagian administrasi.</p>
                    <div class="home-actions">
                        <a class="btn btn-primary" href="{{ route('rooms.index') }}">Jelajahi denah</a>
                        <a class="btn btn-secondary" href="{{ route('booking.guide') }}">Cara booking</a>
                    </div>
                    <div class="home-counts">
                        <span><strong>{{ $totalRuang }}</strong> ruang terdaftar</span>
                        <span><strong>{{ $jumlahPeminjamanSaya }}</strong> pengajuan saya</span>
                    </div>
                </div>

                <aside class="home-preview">
                    <div class="preview-heading">
                        <span>{{ $peminjamanTerdekat ? 'PENGAJUAN TERDEKAT' : 'CONTOH PENGAJUAN' }}</span>
                        @if ($peminjamanTerdekat)
                            @include('partials.status-badge', ['status' => $peminjamanTerdekat->status])
                        @else
                            <span class="preview-status">Alur peminjaman</span>
                        @endif
                    </div>
                    <div class="preview-room">
                        <strong>{{ $peminjamanTerdekat ? $peminjamanTerdekat->room->nama : 'Pilih ruang yang sesuai' }}</strong>
                        <span>{{ $peminjamanTerdekat ? $peminjamanTerdekat->room->lokasi_lengkap : 'Ruang kelas, rapat, atau kegiatan kampus' }}</span>
                    </div>
                    <div class="preview-details">
                        <div>
                            <span>Tanggal</span>
                            <strong>{{ $peminjamanTerdekat ? $peminjamanTerdekat->tanggal->format('d M Y') : 'Tentukan tanggal' }}</strong>
                        </div>
                        <div>
                            <span>Jam</span>
                            <strong>{{ $peminjamanTerdekat ? substr($peminjamanTerdekat->jam_mulai, 0, 5) . '–' . substr($peminjamanTerdekat->jam_selesai, 0, 5) : 'Atur sesuai kebutuhan' }}</strong>
                        </div>
                    </div>
                    <div class="preview-progress" aria-label="Tahapan peminjaman">
                        <span class="complete"></span><span class="{{ $peminjamanTerdekat ? 'complete' : '' }}"></span><span class="{{ $peminjamanTerdekat && $peminjamanTerdekat->status === 'disetujui' ? 'complete' : '' }}"></span>
                    </div>
                    <div class="preview-progress-labels">
                        <span>Dipilih</span><span>Diajukan</span><span>Disetujui</span>
                    </div>
                    @if ($peminjamanTerdekat)
                        <a class="preview-link" href="{{ route('bookings.show', $peminjamanTerdekat) }}">Lihat detail pengajuan <span aria-hidden="true">→</span></a>
                    @endif
                </aside>
            </section>

            <section class="home-benefits" aria-labelledby="home-benefits-title">
                <div class="home-section-heading">
                    <span class="eyebrow">KENAPA PINJAM RUANG</span>
                    <h2 id="home-benefits-title">Semua urusan peminjaman di satu tempat</h2>
                </div>
                <div class="home-benefit-grid">
                    <article class="home-benefit">
                        <h3>Cek ketersediaan</h3>
                        <p>Lihat denah kampus atau daftar ruang berdasarkan kapasitas dan fasilitas yang tersedia.</p>
                    </article>
                    <article class="home-benefit">
                        <h3>Ajukan dari mana saja</h3>
                        <p>Isi jadwal, jumlah peserta, dan keperluan langsung melalui sistem.</p>
                    </article>
                    <article class="home-benefit">
                        <h3>Status jelas</h3>
                        <p>Pantau pengajuan di menu Peminjaman Saya, dari menunggu sampai disetujui.</p>
                    </article>
                </div>
            </section>

            <section class="home-how" aria-labelledby="home-how-title">
                <div class="home-section-heading">
                    <span class="eyebrow">ALUR SINGKAT</span>
                    <h2 id="home-how-title">Tiga langkah sampai ruang siap dipakai</h2>
                </div>
                <div class="home-steps">
                    <article class="home-step">
                        <span class="step-number">1</span>
                        <h3>Pilih ruang</h3>
                        <p>Cari di denah atau daftar ruang.</p>
                    </article>
                    <article class="home-step">
                        <span class="step-number">2</span>
                        <h3>Isi pengajuan</h3>
                        <p>Tentukan jadwal dan keperluan.</p>
                    </article>
                    <article class="home-step">
                        <span class="step-number">3</span>
                        <h3>Pantau status</h3>
                        <p>Tunggu persetujuan dari admin.</p>
                    </article>
                </div>
            </section>
        </div>
    @else
        @include('partials.page-header', [
            'eyebrow' => 'Ringkasan',
            'title' => 'Dashboard',
            'subtitle' => 'Ringkasan peminjaman ruang kampus.',
        ])
        <div class="stats">
            @include('partials.stat-card', ['label' => 'Total Ruang', 'value' => $totalRuang])
            @include('partials.stat-card', ['label' => 'Peminjaman Menunggu', 'value' => $peminjamanMenunggu])
            @include('partials.stat-card', ['label' => 'Peminjaman Hari Ini', 'value' => $peminjamanHariIni])
        </div>
    @endif
@endsection
