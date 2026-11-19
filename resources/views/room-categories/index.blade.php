@extends('layouts.app')

@section('title', 'Kategori Ruang')

@section('content')
    <h1>Kategori Ruang</h1>
    <p><a href="{{ route('room-categories.create') }}" class="btn btn-primary">+ Tambah Kategori</a></p>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Deskripsi</th>
                <th>Jumlah Ruang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td>{{ $category->nama }}</td>
                    <td>{{ $category->deskripsi ?? '-' }}</td>
                    <td>{{ $category->rooms_count }}</td>
                    <td>
                        <a class="btn btn-primary" href="{{ route('room-categories.edit', $category) }}">Ubah</a>
                        <form action="{{ route('room-categories.destroy', $category) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus kategori ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada kategori ruang.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $categories->links() }}
@endsection
