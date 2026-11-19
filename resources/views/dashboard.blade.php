@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @include('partials.page-header', [
        'eyebrow' => 'Ringkasan',
        'title' => 'Dashboard',
        'subtitle' => 'Ringkasan peminjaman ruang kampus.',
    ])

    <div class="stats">
        @include('partials.stat-card', ['label' => 'Total Ruang', 'value' => $totalRuang])

        @if (in_array(auth()->user()->role, ['admin', 'petugas'], true))
            @include('partials.stat-card', ['label' => 'Peminjaman Menunggu', 'value' => $peminjamanMenunggu])
            @include('partials.stat-card', ['label' => 'Peminjaman Hari Ini', 'value' => $peminjamanHariIni])
        @else
            @include('partials.stat-card', ['label' => 'Peminjaman Saya', 'value' => $jumlahPeminjamanSaya])
            @include('partials.stat-card', ['label' => 'Peminjaman Terdekat', 'value' => $peminjamanTerdekat ? $peminjamanTerdekat->tanggal->format('d-m-Y') : '-'])
        @endif
    </div>
@endsection
