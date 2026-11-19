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

        <section>
            <div class="sectionhead">
                <div>
                    <h2>Jelajahi berdasarkan denah</h2>
                    <p>Pilih lantai dan ruang untuk melihat informasi ruangan.</p>
                </div>
            </div>

            <div class="workspace">
                <div class="mapcard">
                    <div class="maptop">
                        <div class="building">
                            {{ $gedungAktif }}
                            <small>Jl. Kampus Raya · Area Akademik</small>
                        </div>
                        <div class="floor-switch">
                            @foreach ([1, 2, 3] as $lantai)
                                <a class="{{ $lantaiAktif === $lantai ? 'active' : '' }}"
                                   href="{{ route('rooms.index', ['gedung' => $gedungAktif, 'lantai' => $lantai]) }}">
                                    Lantai {{ $lantai }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="floorplan">
                        @forelse ($ruangDenah as $index => $room)
                            <a class="roompin position-{{ $index + 1 }}"
                               href="{{ route('rooms.show', $room) }}"
                               title="{{ $room->nama }}">
                                <span>{{ $index + 1 }}</span>
                            </a>
                        @empty
                            <div class="empty">Belum ada ruang pada lantai ini.</div>
                        @endforelse
                    </div>
                </div>

                @php($ruangTerpilih = $ruangDenah->first())
                <aside class="detailcard">
                    @if ($ruangTerpilih)
                        @if (! empty($ruangTerpilih->foto_url))
                            <div class="detailimage"><img src="{{ $ruangTerpilih->foto_url }}" alt="{{ $ruangTerpilih->nama }}"></div>
                        @else
                            <div class="detailimage">⌂</div>
                        @endif
                        <div class="detailbody">
                            @include('partials.status-badge', ['status' => $ruangTerpilih->status])
                            <h2>{{ $ruangTerpilih->nama }}</h2>
                            <p class="page-intro">{{ $ruangTerpilih->lokasi }}</p>
                            <div class="stats">
                                @include('partials.stat-card', ['label' => 'Kapasitas', 'value' => $ruangTerpilih->kapasitas . ' orang'])
                                @include('partials.stat-card', ['label' => 'Luas', 'value' => ($ruangTerpilih->luas_m2 ? $ruangTerpilih->luas_m2 . ' m²' : '-')])
                                @include('partials.stat-card', ['label' => 'Lantai', 'value' => ($ruangTerpilih->lantai ? 'Lantai ' . $ruangTerpilih->lantai : '-')])
                            </div>
                            <div class="chips">
                                @forelse (array_filter(array_map('trim', explode(',', (string) $ruangTerpilih->fasilitas))) as $fasilitas)
                                    <span class="chip">{{ $fasilitas }}</span>
                                @empty
                                    <span class="chip">Fasilitas belum tersedia</span>
                                @endforelse
                            </div>
                            @if ($ruangTerpilih->status === 'tersedia')
                                <p><a class="btn btn-primary btn-full" href="{{ route('rooms.show', $ruangTerpilih) }}">Lihat detail dan booking →</a></p>
                            @endif
                        </div>
                    @else
                        @include('partials.empty-state', ['text' => 'Belum ada ruang pada lantai ini.'])
                    @endif
                </aside>
            </div>
        </section>
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
