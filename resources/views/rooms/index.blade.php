@extends('layouts.app')

@section('title', 'Jelajahi Ruang')

@section('content')
    @include('partials.page-header', [
        'eyebrow' => 'ROOM DISCOVERY',
        'title' => 'Temukan ruang yang tepat untuk setiap ide.',
        'subtitle' => 'Jelajahi gedung, pilih lantai, dan temukan ruangan kampus yang tersedia.',
    ])

    @if (in_array(auth()->user()->role, ['admin', 'petugas'], true))
        <p><a href="{{ route('rooms.create') }}" class="btn btn-primary">+ Tambah Ruang</a></p>
    @endif

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
            <input type="date" name="tanggal" id="tanggal" value="{{ request('tanggal', now()->toDateString()) }}">
        </div>
        <div class="searchfield">
            <label for="lantai">Lantai</label>
            <select name="lantai" id="lantai">
                @foreach ([1, 2, 3] as $lantai)
                    <option value="{{ $lantai }}" @selected($lantaiAktif === $lantai)>Lantai {{ $lantai }}</option>
                @endforeach
            </select>
        </div>
        <div class="searchfield">
            <label for="search">Pencarian</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nama atau kode">
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
                               href="{{ route('rooms.index', array_merge(request()->except('lantai'), ['gedung' => $gedungAktif, 'lantai' => $lantai])) }}">
                                Lantai {{ $lantai }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="floorplan">
                    @forelse ($ruangDenah as $index => $room)
                        <a class="roompin position-{{ $index + 1 }} {{ request('room_id') == $room->id ? 'active' : '' }}"
                           href="{{ route('rooms.index', array_merge(request()->except('room_id'), ['room_id' => $room->id])) }}"
                           title="{{ $room->nama }}">
                            <span>{{ $index + 1 }}</span>
                        </a>
                    @empty
                        <div class="empty">Belum ada ruang pada lantai ini.</div>
                    @endforelse
                </div>
            </div>

            @php
                $ruangTerpilih = $ruangDenah->firstWhere('id', (int) request('room_id')) ?? $ruangDenah->first();
            @endphp

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

                        @if ($ruangTerpilih->status === 'tersedia' && auth()->user()->role === 'peminjam')
                            <p><a class="btn btn-primary btn-full" href="{{ route('rooms.show', $ruangTerpilih) }}">Lihat detail dan booking →</a></p>
                        @else
                            <p><a class="btn btn-secondary btn-full" href="{{ route('rooms.show', $ruangTerpilih) }}">Lihat detail ruang →</a></p>
                        @endif
                    </div>
                @else
                    @include('partials.empty-state', ['text' => 'Pilih lantai yang memiliki ruang.'])
                @endif
            </aside>
        </div>
    </section>

    <section>
        <div class="sectionhead">
            <div>
                <h2>Ruangan di lantai ini</h2>
                <p>Klik ruang untuk melihat ringkasan detailnya.</p>
            </div>
        </div>

        <div class="rooms-grid">
            @forelse ($ruangDenah as $room)
                @include('partials.room-card', ['room' => $room])
            @empty
                @include('partials.empty-state', ['text' => 'Belum ada ruangan pada lantai ini.'])
            @endforelse
        </div>
    </section>
@endsection
