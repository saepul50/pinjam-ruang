@extends('layouts.app')

@section('title', 'Kategori Ruang')

@section('content')
    @include('partials.page-header', [
        'eyebrow' => 'Master Data',
        'title' => 'Kategori Ruang',
        'subtitle' => 'Kelola pengelompokan ruang kampus.',
    ])

    <p><a href="{{ route('room-categories.create') }}" class="btn btn-primary">+ Tambah Kategori</a></p>

    <div class="card table-wrap">
        <table class="table">
            <thead>
                <tr><th>Nama</th><th>Deskripsi</th><th>Jumlah Ruang</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->nama }}</td>
                        <td>{{ $category->deskripsi ?? '-' }}</td>
                        <td>{{ $category->rooms_count }}</td>
                        <td>
                            <a class="btn btn-sm btn-secondary" href="{{ route('room-categories.edit', $category) }}">Ubah</a>
                            @include('partials.confirm-delete', [
                                'action' => route('room-categories.destroy', $category),
                                'label' => 'Hapus',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4"><div class="empty">Belum ada kategori ruang.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">{{ $categories->links() }}</div>
@endsection
