@extends('layouts.app')

@section('title', 'Cara Booking')

@section('content')
    @include('partials.page-header', [
        'eyebrow' => 'PANDUAN PEMINJAMAN',
        'title' => 'Cara Booking Ruang',
        'subtitle' => 'Ikuti langkah berikut agar pengajuan peminjaman dapat diproses.',
    ])

    <div class="stats">
        @include('partials.stat-card', ['label' => '1', 'value' => 'Pilih Ruang'])
        @include('partials.stat-card', ['label' => '2', 'value' => 'Isi Jadwal'])
        @include('partials.stat-card', ['label' => '3', 'value' => 'Tunggu Persetujuan'])
    </div>

    <section class="card dashboard-action-card">
        <h2>Langkah Pengajuan</h2>
        <ol>
            <li>Buka <strong>Jelajahi Ruang</strong> atau <strong>Daftar Ruang</strong>.</li>
            <li>Pilih ruangan yang berstatus <strong>Tersedia</strong>.</li>
            <li>Isi tanggal, jam mulai, jam selesai, keperluan, dan jumlah peserta.</li>
            <li>Kirim pengajuan lalu pantau status di <strong>Peminjaman Saya</strong>.</li>
            <li>Peminjaman hanya dapat dibatalkan saat status masih <strong>Menunggu</strong>.</li>
        </ol>
        <a class="btn btn-primary" href="{{ route('rooms.list') }}">Lihat Daftar Ruang →</a>
    </section>
@endsection
