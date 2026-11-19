@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>Dashboard</h1>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="value">{{ $totalRuang }}</div>
            <div class="label">Total Ruang</div>
        </div>

        @if (in_array(auth()->user()->role, ['admin', 'petugas'], true))
            <div class="stat-card">
                <div class="value">{{ $peminjamanMenunggu }}</div>
                <div class="label">Peminjaman Menunggu</div>
            </div>
            <div class="stat-card">
                <div class="value">{{ $peminjamanHariIni }}</div>
                <div class="label">Peminjaman Hari Ini</div>
            </div>
        @else
            <div class="stat-card">
                <div class="value">{{ $jumlahPeminjamanSaya }}</div>
                <div class="label">Peminjaman Saya</div>
            </div>
            <div class="stat-card">
                <div class="value">
                    {{ $peminjamanTerdekat ? $peminjamanTerdekat->tanggal->format('d-m-Y') : '-' }}
                </div>
                <div class="label">Peminjaman Terdekat</div>
            </div>
        @endif
    </div>
@endsection
