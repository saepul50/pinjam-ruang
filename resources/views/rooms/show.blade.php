@extends('layouts.app')

@section('title', $room->nama)

@section('content')
    <h1>{{ $room->nama }}</h1>
    <p><a href="{{ route('rooms.index') }}">&larr; Kembali ke daftar ruang</a></p>

    <table>
        <tr><th>Kode</th><td>{{ $room->kode }}</td></tr>
        <tr><th>Kategori</th><td>{{ $room->roomCategory->nama }}</td></tr>
        <tr><th>Kapasitas</th><td>{{ $room->kapasitas }} orang</td></tr>
        <tr><th>Lokasi</th><td>{{ $room->lokasi }}</td></tr>
        <tr><th>Fasilitas</th><td>{{ $room->fasilitas ?? '-' }}</td></tr>
        <tr><th>Deskripsi</th><td>{{ $room->deskripsi ?? '-' }}</td></tr>
        <tr><th>Status</th><td>{{ ucfirst($room->status) }}</td></tr>
    </table>

    <h2>Jadwal Terisi</h2>
    <form method="GET" action="{{ route('rooms.show', $room) }}">
        <label for="tanggal">Tanggal</label>
        <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}">
        <button type="submit" class="btn btn-primary">Tampilkan</button>
    </form>

    <table>
        <thead>
            <tr><th>Jam Mulai</th><th>Jam Selesai</th><th>Status</th></tr>
        </thead>
        <tbody>
            @forelse ($jadwalTerisi as $jadwal)
                <tr>
                    <td>{{ $jadwal->jam_mulai }}</td>
                    <td>{{ $jadwal->jam_selesai }}</td>
                    <td><span class="badge badge-{{ $jadwal->status }}">{{ ucfirst($jadwal->status) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="3">Belum ada jadwal terisi pada tanggal ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if ($room->status === 'tersedia')
        <p><a href="{{ route('bookings.create') }}?room_id={{ $room->id }}" class="btn btn-success">Ajukan Peminjaman</a></p>
    @endif
@endsection
