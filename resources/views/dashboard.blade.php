@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if (auth()->user()->role === 'peminjam')
        @include('partials.page-header', [
            'eyebrow' => 'ROOM DISCOVERY',
            'title' => 'Temukan ruang yang tepat untuk setiap ide.',
            'subtitle' => 'Jelajahi gedung, pilih lantai, dan temukan ruangan kampus yang tersedia.',
        ])

        <form class="searchbar" method="GET" action="{{ route('rooms.index') }}">
            <div class="searchfield">
                <label for="gedung">Gedung</label>
                <select name="gedung" id="gedung">
                    @foreach ($gedungs as $gedung)
                        <option value="{{ $gedung }}" @selected($gedungAktif === $gedung)>{{ $gedung }}</option>
                    @endforeach
                </select>
            </div>
            <div class="searchfield">
                <label for="tanggal">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" value="{{ now()->toDateString() }}">
            </div>
            <div class="searchfield">
                <label for="lantai">Lantai</label>
                <select name="lantai" id="lantai">
                    @foreach ([1, 2, 3] as $lantai)
                        <option value="{{ $lantai }}" @selected($lantaiAktif === $lantai)>Lantai {{ $lantai }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Cari Ruangan →</button>
        </form>

        @include('partials.explore-workspace')
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
