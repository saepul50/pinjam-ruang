@extends('layouts.app')

@section('title', 'Tambah Kategori Ruang')

@section('content')
    <h1>Tambah Kategori Ruang</h1>
    <p><a href="{{ route('room-categories.index') }}">&larr; Kembali</a></p>
    <form action="{{ route('room-categories.store') }}" method="POST">
        @include('room-categories._form')
    </form>
@endsection
