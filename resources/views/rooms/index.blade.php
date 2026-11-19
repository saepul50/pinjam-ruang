@extends('layouts.app')

@section('title', 'Daftar Ruang')

@section('content')
    @include('partials.page-header', [
        'eyebrow' => 'DAFTAR RUANG',
        'title' => 'Semua Ruangan Kampus',
        'subtitle' => 'Lihat daftar, kapasitas, lokasi, dan status setiap ruang.',
    ])

    @if (in_array(auth()->user()->role, ['admin', 'petugas'], true))
        <p><a href="{{ route('rooms.create') }}" class="btn btn-primary">+ Tambah Ruang</a></p>
    @endif

    <form class="searchbar" method="GET" action="{{ route('rooms.list') }}">
        <div class="searchfield">
            <label for="kategori">Kategori</label>
            <select name="kategori" id="kategori">
                <option value="">Semua</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('kategori') == $category->id)>{{ $category->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="searchfield">
            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="">Semua</option>
                @foreach (['tersedia', 'perbaikan', 'nonaktif'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="searchfield">
            <label for="kapasitas_min">Kapasitas Minimal</label>
            <input type="number" min="1" name="kapasitas_min" id="kapasitas_min" value="{{ request('kapasitas_min') }}">
        </div>
        <div class="searchfield">
            <label for="search">Pencarian</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nama atau lokasi">
        </div>
        <button type="submit" class="btn btn-primary">Cari Ruangan →</button>
    </form>

    <div class="rooms-grid">
        @forelse ($rooms as $room)
            @include('partials.room-card', ['room' => $room])
        @empty
            @include('partials.empty-state', ['text' => 'Tidak ada ruang yang cocok.'])
        @endforelse
    </div>

    <div class="pagination">{{ $rooms->links() }}</div>
@endsection
