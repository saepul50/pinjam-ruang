@extends('layouts.app')

@section('title', 'Ubah Kategori Ruang')

@section('content')
    @include('partials.page-header', ['eyebrow' => 'Master Data', 'title' => 'Ubah Kategori', 'subtitle' => 'Perbarui nama dan deskripsi kategori.'])
    <p><a class="btn btn-secondary" href="{{ route('room-categories.index') }}">&larr; Kembali</a></p>
    <form class="card" action="{{ route('room-categories.update', $category) }}" method="POST">
        @method('PUT')
        @include('room-categories._form')
    </form>
@endsection
