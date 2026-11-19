@extends('layouts.app')

@section('title', 'Tambah Ruang')

@section('content')
    @include('partials.page-header', ['eyebrow' => 'Kelola Ruang', 'title' => 'Tambah Ruang', 'subtitle' => 'Lengkapi data ruang yang dapat dipinjam.'])
    <p><a class="btn btn-secondary" href="{{ route('rooms.index') }}">&larr; Kembali</a></p>
    <form class="card" action="{{ route('rooms.store') }}" method="POST">
        @include('rooms._form')
    </form>
@endsection
