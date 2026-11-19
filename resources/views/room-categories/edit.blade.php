@extends('layouts.app')

@section('title', 'Ubah Kategori Ruang')

@section('content')
    <h1>Ubah Kategori Ruang</h1>
    <p><a href="{{ route('room-categories.index') }}">&larr; Kembali</a></p>
    <form action="{{ route('room-categories.update', $category) }}" method="POST">
        @method('PUT')
        @include('room-categories._form')
    </form>
@endsection
