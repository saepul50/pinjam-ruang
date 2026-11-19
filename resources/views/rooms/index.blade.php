@extends('layouts.app')

@section('title', 'Daftar Ruang')

@section('content')
    <h1>Daftar Ruang</h1>

    @if (in_array(auth()->user()->role, ['admin', 'petugas'], true))
        <p><a href="{{ route('rooms.create') }}" class="btn btn-primary">+ Tambah Ruang</a></p>
    @endif

    <form method="GET" action="{{ route('rooms.index') }}">
        <label for="kategori">Kategori</label>
        <select name="kategori" id="kategori">
            <option value="">Semua</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('kategori') == $category->id)>
                    {{ $category->nama }}
                </option>
            @endforeach
        </select>

        <label for="status">Status</label>
        <select name="status" id="status">
            <option value="">Semua</option>
            @foreach (['tersedia', 'perbaikan', 'nonaktif'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>

        <label for="kapasitas_min">Kapasitas Minimal</label>
        <input type="number" min="1" name="kapasitas_min" id="kapasitas_min" value="{{ request('kapasitas_min') }}">

        <label for="search">Pencarian</label>
        <input type="text" name="search" id="search" value="{{ request('search') }}">

        <button type="submit" class="btn btn-primary">Cari</button>
        <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Reset</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Kapasitas</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rooms as $room)
                <tr>
                    <td>{{ $room->kode }}</td>
                    <td>{{ $room->nama }}</td>
                    <td>{{ $room->roomCategory->nama }}</td>
                    <td>{{ $room->kapasitas }}</td>
                    <td>{{ $room->lokasi }}</td>
                    <td>{{ ucfirst($room->status) }}</td>
                    <td>
                        <a class="btn btn-primary" href="{{ route('rooms.show', $room) }}">Detail</a>
                        @if (in_array(auth()->user()->role, ['admin', 'petugas'], true))
                            <a class="btn btn-secondary" href="{{ route('rooms.edit', $room) }}">Ubah</a>
                            <form action="{{ route('rooms.destroy', $room) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus ruang ini?')">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">Tidak ada ruang yang cocok.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $rooms->links() }}
@endsection
