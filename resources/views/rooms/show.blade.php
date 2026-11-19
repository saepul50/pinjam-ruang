@extends('layouts.app')

@section('title', $room->nama)

@section('content')
    @include('partials.page-header', [
        'eyebrow' => 'Detail Ruang',
        'title' => $room->nama,
        'subtitle' => $room->lokasi,
    ])

    <p><a class="btn btn-secondary" href="{{ route('rooms.index') }}">&larr; Kembali ke daftar ruang</a></p>

    <div class="workspace">
        <section class="card">
            <h2>Jadwal Terisi</h2>
            <form class="searchbar" method="GET" action="{{ route('rooms.show', $room) }}">
                <div class="searchfield">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}">
                </div>
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </form>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr><th>Jam Mulai</th><th>Jam Selesai</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($jadwalTerisi as $jadwal)
                            <tr>
                                <td>{{ $jadwal->jam_mulai }}</td>
                                <td>{{ $jadwal->jam_selesai }}</td>
                                <td>@include('partials.status-badge', ['status' => $jadwal->status])</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">Belum ada jadwal terisi pada tanggal ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="detailcard">
            @if (! empty($room->foto_url))
                <div class="detailimage"><img src="{{ $room->foto_url }}" alt="{{ $room->nama }}"></div>
            @else
                <div class="detailimage">⌂</div>
            @endif

            <div class="detailbody">
                @include('partials.status-badge', ['status' => $room->status])
                <h2>{{ $room->nama }}</h2>
                <p class="page-intro">{{ $room->lokasi }}</p>

                <div class="stats">
                    @include('partials.stat-card', ['label' => 'Kapasitas', 'value' => $room->kapasitas . ' orang'])
                    @include('partials.stat-card', ['label' => 'Luas', 'value' => ($room->luas_m2 ? $room->luas_m2 . ' m²' : '-')])
                    @include('partials.stat-card', ['label' => 'Lantai', 'value' => ($room->lantai ? 'Lantai ' . $room->lantai : '-')])
                </div>

                <div class="chips">
                    @forelse (array_filter(array_map('trim', explode(',', (string) $room->fasilitas))) as $fasilitas)
                        <span class="chip">{{ $fasilitas }}</span>
                    @empty
                        <span class="chip">Fasilitas belum tersedia</span>
                    @endforelse
                </div>

                @if ($room->status === 'tersedia')
                    <p><a href="{{ route('bookings.create') }}?room_id={{ $room->id }}" class="btn btn-primary btn-full">Ajukan Peminjaman →</a></p>
                @endif
            </div>
        </aside>
    </div>
@endsection
