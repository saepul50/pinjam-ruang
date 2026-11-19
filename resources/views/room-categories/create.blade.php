@extends('layouts.app')

@section('title', 'Tambah Kategori Ruang')

@section('content')
    @include('partials.page-header', ['eyebrow' => 'Master Data', 'title' => 'Tambah Kategori', 'subtitle' => 'Tambahkan kategori ruang baru.'])
    <p><a class="btn btn-secondary" href="{{ route('room-categories.index') }}">&larr; Kembali</a></p>
    <form class="card" action="{{ route('room-categories.store') }}" method="POST">
        @include('room-categories._form')
    </form>
@endsection
